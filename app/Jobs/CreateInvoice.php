<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Tenant\Invoice\Invoice;
use App\Tenant\Invoice\Item;
use App\Tenant\Invoice\Recurring;
use App\Tenant\Invoice\Status;
use DB;
use App\Team;
use TeamManager;
use App\Facades\TeamConfig;
use App\TeamRecurringInvoice;
use Mail;
use App\Mail\SendDocument;

class CreateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recurring;

    public $teamInvoice;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TeamRecurringInvoice $teamInvoice)
    {
        $this->teamInvoice = $teamInvoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$team = Team::findOrFail($this->teamInvoice->team_id);

    	TeamManager::setTeam($team);

    	$this->recurring = Recurring::findOrFail($this->teamInvoice->recurring_id);

        $r = $this->recurring;
        $d = new Invoice;
        $d->contact_id = $r->contact_id;
        $d->issue_date = now()->format('Y-m-d');
        $d->due_in_days = $r->due_after;
        $d->due_date = now()->addDays($r->due_after ?? 0)->format('Y-m-d');
        $d->reference = $r->reference;
        $d->discount = $r->discount;
        $d->grand_total = $r->grand_total;
        $d->sub_total = $r->sub_total;
        $d->tax = $r->tax;
        $d->tax_total = $r->tax_total;
        $d->tax_2 = $r->tax_2;
        $d->tax_2_total = $r->tax_2_total;
        $d->term_id = $r->term_id;

        $id = TeamConfig::get('invoice_status_on_create_id');

        if($id) {
            $c = Status::findOrFail($id);
            $d->status_id = $c->id;
        } else {
            $d->status_id = Status::first()->id;
        }

        $d = DB::transaction(function() use ($d, $r) {
            $c = counter('invoice');
            $d->number = $c->number;
            $d->payment_code = $this->getNewPaymentCode();
            $d->storeHasMany([
                'lines' => $r->lines->toArray()
            ]);
            $c->increment('value');
            $next = now()->format('Y-m-d');
            $r->last_generated_date = $next;
            $r->save();
            $this->teamInvoice->last_generated_date = now()->setTimezone('UTC')->format('Y-m-d');
            $this->teamInvoice->save();

            return $d;
        });


        // email
        $team = TeamManager::getTeam();

        $allowedVars = [
            'number' => $d->number,
            'contact_name' => $d->contact->name,
            'company' => TeamConfig::get('company_name'),
            'due_date' => formatDate($d->due_date),
            'grand_total' => formatMoney($d->grand_total),
            'payment_link' => route('app.invoice.show', ['team' => $team->uuid, 'invoice' => $d->payment_code])
        ];

        $subject = parseStringTemplate(
            TeamConfig::get('invoice_email_subject'),
            $allowedVars
        );

        $messageTemplate = TeamConfig::get('invoice_email_template');

        if(!$team->hasPaymentGateway()) {
            $messageTemplate = preg_replace("/@paymentGateway[\s\S]+?@endPaymentGateway/", '', $messageTemplate);
        } else {
            $messageTemplate = preg_replace('/(@paymentGateway|@endPaymentGateway)/im', '', $messageTemplate);
        }

        $message = parseStringTemplate(
            $messageTemplate,
            $allowedVars
        );

        $info = [
            'email_to' => $d->contact->email,
            'bcc' => null,
            'subject' =>  $subject,
            'message' => $message
        ];



        Mail::send(new SendDocument($d, $info, 'invoice', $team));

        if($id = TeamConfig::get('invoice_status_on_sent_id')) {
            $d->status_id = $id;
            $d->save();
        }
    }

    public function getNewPaymentCode()
    {
        $name = null;
        do {
            $name = str_random(32);
        } while(Invoice::where('payment_code', $name)->first());

        return 'inv_'.$name;
    }
}
