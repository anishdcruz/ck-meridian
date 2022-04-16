<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tenant\Quotation\Quotation;
use App\Tenant\Invoice\Invoice;
use App\Facades\TeamConfig;
use Mail;
use App\Mail\SendDocument;
use App\Mail\SendNotification;
use TeamManager;
use App\Tenant\Refund\Refund;
use App\Tenant\Payment\Payment;

class EmailController extends Controller
{
    public function invoicesGet($id)
    {
        $model = Invoice::findOrFail($id);
        $user = auth()->user();
        $team = $user->currentTeam();

        $warning = null;

        $allowedVars = [
            'number' => $model->number,
            'contact_name' => $model->contact->name,
            'company' => TeamConfig::get('company_name'),
            'due_date' => formatDate($model->due_date),
            'grand_total' => formatMoney($model->grand_total),
            'payment_link' => route('app.invoice.show', ['team' => $team->uuid, 'invoice' => $model->payment_code])
        ];

        $subject = parseStringTemplate(
            TeamConfig::get('invoice_email_subject'),
            $allowedVars
        );

        $messageTemplate = TeamConfig::get('invoice_email_template');




        $message = parseStringTemplate(
            $messageTemplate,
            $allowedVars
        );

        $form = [
            'email_to' => $model->contact->email,
            'bcc' => $user->email,
            'subject' =>  $subject,
            'message' => $message
        ];

        return [
            'form' => $form,
            'warning' => $warning
        ];
    }

    public function invoicesPost($id, Request $request)
    {
    	abort_in_demo();
        $info = $request->validate([
            'email_to' => 'required|email',
            'bcc' => 'nullable|email',
            'subject' => 'required',
            'message' => 'required'
        ]);

        $model = Invoice::with([
            'status:id,name,color', 'lines.item.uom',
            'contact', 'term'
            ])
            ->findOrFail($id);

        $team = TeamManager::getTeam();

        Mail::send(new SendDocument($model, $info, 'invoice', $team));

        if($id = TeamConfig::get('invoice_status_on_sent_id')) {
            $model->status_id = $id;
            $model->save();
        }

        return [
            'saved' => true
        ];
    }

    public function refundsGet($id)
    {
        $model = Refund::findOrFail($id);
        $user = auth()->user();
        $team = $user->currentTeam();

        $warning = null;

        $allowedVars = [
            'number' => $model->number,
            'contact_name' => $model->contact->name,
            'company' => TeamConfig::get('company_name'),
            'refund_date' => formatDate($model->refund_date),
            'amount_refunded' => formatMoney($model->amount),
            'payment_number' => $model->payment->number,
            'amount_received' => formatMoney($model->payment->amount_received)
        ];

        $subject = parseStringTemplate(
            TeamConfig::get('refund_email_subject'),
            $allowedVars
        );

        $message = parseStringTemplate(
            TeamConfig::get('refund_email_template'),
            $allowedVars
        );

        $form = [
            'email_to' => $model->contact->email,
            'bcc' => $user->email,
            'subject' =>  $subject,
            'message' => $message
        ];

        return [
            'form' => $form,
            'warning' => $warning
        ];
    }

    public function refundsPost($id, Request $request)
    {
    	abort_in_demo();
        $info = $request->validate([
            'email_to' => 'required|email',
            'bcc' => 'nullable|email',
            'subject' => 'required',
            'message' => 'required'
        ]);

        Mail::send(new SendNotification($info));

        return [
            'saved' => true
        ];
    }

    public function paymentsGet($id, Request $request)
    {
        $model = Payment::findOrFail($id);
        $user = auth()->user();
        $team = $user->currentTeam();

        $warning = null;

        $allowedVars = [
            'number' => $model->number,
            'contact_name' => $model->contact->name,
            'company' => TeamConfig::get('company_name'),
            'payment_date' => formatDate($model->payment_date),
            'amount_received' => formatMoney($model->amount_received),
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
            'email_to' => $model->contact->email,
            'bcc' => $user->email,
            'subject' =>  $subject,
            'message' => $message
        ];

        return [
            'form' => $form,
            'warning' => $warning
        ];
    }

    public function paymentsPost($id, Request $request)
    {
    	abort_in_demo();
        $info = $request->validate([
            'email_to' => 'required|email',
            'bcc' => 'nullable|email',
            'subject' => 'required',
            'message' => 'required'
        ]);

        $model = Payment::with([
            'contact', 'invoice'
            ])
            ->findOrFail($id);

        $team = TeamManager::getTeam();

        Mail::send(new SendDocument($model, $info, 'payment', $team));

        return [
            'saved' => true
        ];
    }
}
