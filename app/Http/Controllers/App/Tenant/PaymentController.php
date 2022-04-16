<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tenant\Payment\Payment;
use Stripe\Stripe;
use Stripe\Refund as StripeRefund;
use Stripe\Charge;
use Exception;
use TeamManager;
use App\Tenant\Refund\Refund;
use DB;
use App\Support\PDF;
use App\Mail\SendNotification;
use Mail;
use App\Facades\TeamConfig;
use App\Tenant\Invoice\Invoice;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
	public function search()
	{
        $collection = Payment::search();

		return [
    		'collection' => $collection
    	];
	}

    public function index()
    {
        $collection = Payment::with([
            'contact:id,name,organization', 'invoice:id,number',
        ])->when(request('contact_id'), function($query) {
            return $query->where('contact_id', request('contact_id'));
        })->when(request('invoice_id'), function($query) {
            return $query->where('invoice_id', request('invoice_id'));
        })->filter();

        return [
            'collection' => $collection
        ];
    }

    public function show($id)
    {
        $pp = Payment::with([
            'contact', 'invoice'
            ])
            ->findOrFail($id);

        return [
            'model' => $pp
        ];
    }

    public function store(Request $request)
    {

    	abort_in_demo();
    	$this->authorize('module', 'payments.create');
        $request->validate([
            'invoice_id' => 'required|integer',
            'payment_date' => 'required|date_format:Y-m-d',
            'method' => 'required',
            'amount_received' => 'required|numeric|min:1',
            'transaction_fees' => 'required|numeric|min:0',
            'note' => 'required'
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        $payment = new Payment;
        $payment->contact_id = $invoice->contact_id;
        $payment->invoice_id = $invoice->id;
        $payment->payment_date = $request->payment_date;
        $payment->method = $request->method;
        $payment->amount_received = $request->amount_received;
        $payment->transaction_fees = $request->transaction_fees;
        $payment->net_amount = $request->amount_received - $request->transaction_fees;
        $payment->note = $request->note;

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

            $contact = $payment->contact;
            $contact->total_revenue += $payment->amount_received;
            $contact->save();
            return $payment;
        });

        return [
            'saved' => true,
            'id' => $payment->id
        ];
    }

    public function refund($id, Request $request)
    {
    	abort_in_demo();
    	$this->authorize('module', 'payments.refund');
        $payment = Payment::findOrFail($id);
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'reason' => 'required',
            'description' => 'required'
        ]);

        $team = TeamManager::getTeam();

        // update payment
        $refund = new Refund;
        $refund->contact_id = $payment->contact_id;
        $refund->payment_id = $payment->id;
        $refund->refund_date = now()->format('Y-m-d');

        if($payment->method === 'stripe') {
            Stripe::setApiKey(config('services.stripe.secret'));
            $extra = [];
            if(inSAASMode()) {
            	$extra = ['stripe_account' => $team->stripe_user_id];
            	Stripe::setClientId(config('services.stripe.connect_client_id'));
            }


            try {
                $re = StripeRefund::create([
                  "charge" => $payment->charge_id,
                  'amount' => formatMoneyStripe($request->amount),
                  'reason' => $request->reason,
                  'expand' => ['balance_transaction', 'charge']
                ], $extra);
            } catch(Exception $e) {
                abort(404, 'Could not process stripe refund');
            }

            $refund->refund_id = $re->id;
            $refund->status = $re->status;
            $refund->amount = fromStripeAmount($re->amount);
        } else {
            $refund->amount = $request->amount;
        }

        $refund->reason = $request->reason;
        $refund->description = $request->description;

        $refund = DB::transaction(function() use ($refund) {
            $c = counter('refund');
            $refund->number = $c->number;
            $refund->save();
            $c->increment('value');
            return $refund;
        });


        if($payment->method == 'stripe') {
            try {
                $charge = Charge::retrieve([
                    'id'  => $payment->charge_id,
                    'expand' => ['balance_transaction']
                ], $extra);
            } catch (Exception $e) {
                abort(404, 'Could not retrieve stripe payment');
            }

            $payment->amount_refunded = fromStripeAmount($charge->amount_refunded);
        } else {
            $payment->amount_refunded = $payment->amount_refunded + $request->amount;
        }

        $payment->save();

        // reduce invoice amount paid
        // set invoice status

        $contact = $payment->contact;
        $contact->total_revenue -= $request->amount;
        $contact->save();

        return [
            'saved' => true,
            'id' => $refund->id
        ];
    }

    public function confirmDestroy($model, $db)
    {
    	$result = confirm_array([
    		check_parent('refunds', 'payment_id', $model)
    	]);


    	if($result) {
             $config = TeamConfig::getMany([
                'invoice_status_on_partially_paid_id'
            ]);

    		$invoice = $model->invoice;
    		$amount = $invoice->amount_paid - $model->amount_received;
            $invoice->amount_paid = $amount;
            $invoice->status_id = $config['invoice_status_on_partially_paid_id'];
            $invoice->save();

    		$contact = $model->contact;
    		$contact->total_revenue -= $model->amount_received;
    		$contact->save();
    	}

    	return $result;
    }

    public function destroy($id)
    {
    	abort_in_demo();
    	$this->authorize('module', 'payments.delete');
    	$found = Payment::findOrFail($id);

    	if(!$this->confirmDestroy($found, new DB)) {
	    	return response()->json([
	        	'message' => __('app.cannot_delete'),
		        'errors' => []
		    ], 422);
    	}

    	$found->delete();

    	return [
    		'deleted' => true,
    		'id' => $id
    	];
    }

    public function downloadPDF($id, Request $request, PDF $pdf)
    {
        $model = Payment::findOrFail($id);
        return $pdf
            ->preview('app.pdf.payment', [
                'model' => $model
            ]);
    }


    public function export(Request $request)
    {
        return Excel::download(new PaymentsExport($request->team()), 'payments.xlsx');
    }
}
