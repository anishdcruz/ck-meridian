<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DB;
use App\Team;
use TeamManager;
use App\Facades\TeamConfig;
use App\TeamRecurringExport;;
use Mail;
use App\Tenant\RecurringExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContactsExport;
use App\Exports\ExpensesExport;
use App\Exports\InvoicesExport;
use App\Exports\ItemsExport;
use App\Exports\PaymentsExport;
use App\Exports\PaymentsMadeExport;
use App\Exports\PurchaseOrdersExport;
use App\Exports\RecurringInvoicesExport;
use App\Exports\RefundsExport;
use App\Exports\VendorsExport;
use App\Exports\QuotationsExport;

class CreateExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $teamExport;

    public $export;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TeamRecurringExport $teamExport)
    {
        $this->teamExport = $teamExport;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $team = Team::findOrFail($this->teamExport->team_id);

    	TeamManager::setTeam($team);

    	$this->export = RecurringExport::findOrFail($this->teamExport->export_id);
    	$map = [
    		'contacts' => ContactsExport::class,
			'items' => ItemsExport::class,
            'quotations' => QuotationsExport::class,
            'invoices' => InvoicesExport::class,
            'payments' => PaymentsExport::class,
            'refunds' => RefundsExport::class,
            'recurring-invoices' => RecurringInvoicesExport::class,
            'vendors' => VendorsExport::class,
            'purchase-orders' => PurchaseOrdersExport::class,
            'payments-made' => PaymentsMadeExport::class,
            'expenses' => ExpensesExport::class
    	];

    	$ex = $map[$this->export->resource];
    	$title = __('app.contacts').'.xlsx';

    	$file = Excel::download(new $ex($team, $this->export), $title)
    		->getFile();

        $data = ['team' => $team, 'export' => $this->export];

        Mail::send('app.mail.export', $data, function($message) use ($file, $title) {
            $message->to($this->export->email_to);
            $message->subject($this->export->name);
            $message->attach($file, [
                'as' => $title
            ]);
        });
    }
}
