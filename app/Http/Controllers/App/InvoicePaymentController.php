<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Team;
use App\Tenant\Invoice\Invoice;
use TeamManager;
use App\Facades\TeamConfig;
use Stripe\Customer;
use Stripe\Charge;
use Exception;
use Stripe\Stripe;
use App\Tenant\Payment\Payment;
use App\Tenant\Payment\Token;
use DB;
use Mail;
use App\Mail\SendDocument;

class InvoicePaymentController extends Controller
{
    public function show($team, $invoice)
    {
    	abort_in_demo();

    	$q = Team::where('uuid', $team);

    	if(inSAASMode()) {
            $q->whereNotNull('stripe_user_id');
    	}

    	$team = $q->firstOrFail();

    	TeamManager::setTeam(
    	    $team
    	);

    	// set team config

    	config([
    	    'app.timezone' => $team->timezone
    	]);

    	date_default_timezone_set($team->timezone);

    	$invoice = Invoice::where('payment_code', $invoice)
    		->whereNull('paid_at')
    		->firstOrFail();

    	$config = TeamConfig::getMany([
			'registration_label',
			'tax_enable',
			'company_name',
			'company_address',
			'company_email',
			'company_telephone',
			'company_fax',
			'company_logo'
		]);

    	return view('app.payments.show', [
    		'invoice' => $invoice,
    		'team' => $team,
    		'config' => $config
    	]);
    }

    public function store(Request $request, $team, $invoice)
    {
    	abort_in_demo();
    	$team = Team::where('uuid', $team)->firstOrFail();

    	TeamManager::setTeam(
    	    $team
    	);

    	config([
    	    'app.timezone' => $team->timezone
    	]);

    	date_default_timezone_set($team->timezone);

    	$invoice = Invoice::where('payment_code', $invoice)
    		->whereNull('paid_at')
    		->firstOrFail();

        $request->validate([
            'stripeToken' => 'required|alpha_dash|unique:team.tokens,token',
            'stripeEmail' => 'required|email',
        ]);

        $token  = $request->stripeToken;
        $email  = $request->stripeEmail;

        Token::create([
            'token' => $token,
            'email' => $email
        ]);

		Stripe::setApiKey(config('services.stripe.secret'));
		$extra = [];
		if(inSAASMode()) {
			$extra = ['stripe_account' => $team->stripe_user_id];
			Stripe::setClientId(config('services.stripe.connect_client_id'));
		}


	  	try {
  			$charge = Charge::create([
  				'source'  => $token,
  		    	'amount'   => formatMoneyStripe($invoice->grand_total),
  		    	'currency' => $team->currency_id,
                'description' => 'Payment for Invoice '.$invoice->number,
                'expand' => ['balance_transaction']
  			], $extra);
	  	} catch (Exception $e) {
			abort(404, 'Could not charge stripe payment');
	  	}

	  	// create payment
        $payment = new Payment;
        $payment->contact_id = $invoice->contact_id;
        $payment->invoice_id = $invoice->id;
        $payment->payment_date = now()->format('Y-m-d');
        $payment->charge_id = $charge->id;
        $payment->amount_received = fromStripeAmount($charge->amount);
        $payment->transaction_fees = fromStripeAmount($charge->balance_transaction->fee);
        $payment->net_amount = fromStripeAmount($charge->balance_transaction->net);
        $payment->status = $charge->status;
        $payment->method = 'stripe';

        $payment = DB::transaction(function() use ($payment, $invoice) {
            $c = counter('payment_received');
            $payment->number = $c->number;
            $payment->save();
            $c->increment('value');

            // set invoice status
            $config = TeamConfig::getMany([
                'invoice_status_on_partially_paid_id',
                'invoice_status_on_paid_id',
            ]);

            $amount = $invoice->amount_paid + $payment->amount_received;
            $invoice->amount_paid = $amount;

            $invoice->status_id = $config['invoice_status_on_partially_paid_id'];

            if($invoice->amount_paid >= $invoice->grand_total) {
                $invoice->status_id = $config['invoice_status_on_paid_id'];
                $invoice->paid_at = $payment->created_at;
                $invoice->save();
            }

            $invoice->save();
            return $payment;
        });

        // sent email
        $allowedVars = [
            'number' => $payment->number,
            'payment_date' => formatDate($payment->payment_date),
            'amount_received' => formatMoney($payment->amount_received),
            'company' => TeamConfig::get('company_name'),
            'contact_name' => $invoice->contact->name,
        ];

        $subject = parseStringTemplate(
            TeamConfig::get('payment_email_subject'),
            $allowedVars
        );

        $message = parseStringTemplate(
            TeamConfig::get('payment_email_template'),
            $allowedVars
        );

        $form = [
            'email_to' => $invoice->contact->email,
            'bcc' => TeamConfig::get('payment_notification_email'),
            'subject' =>  $subject,
            'message' => $message
        ];

        Mail::send(new SendDocument($payment, $form, 'payment', $team));

        return view('app.payments.receipt', [
            'model' => $payment,
            'team' => $team
        ]);
    }
}
