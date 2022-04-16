<?php

namespace App\Services;

use Illuminate\Console\Scheduling\Schedule;
use App\TeamRecurringInvoice;
use App\Jobs\CreateInvoice;
use Schema;

class RecurringInvoiceDispatcher {

	protected $schedule;
	protected $logPath;

	public function __construct(Schedule $schedule)
	{
		$this->schedule = $schedule;
		$this->filePath = storage_path('logs/scheduler.log');
		$this->work();
	}

	public function work()
	{
		$invoices = $this->getInvoices();
		foreach($invoices as $invoice) {
			$frequency = $invoice->frequency.'On';
			$this->schedule->job(new CreateInvoice($invoice))
			    ->$frequency($invoice->send_on, $invoice->send_at)
			    ->timezone('UTC')
			    ->appendOutputTo($this->filePath);
		}
	}

	protected function getInvoices()
	{
        $now = now()->setTimezone('UTC')->format('Y-m-d');

        return $invoices = TeamRecurringInvoice::where('start_date', '<=', $now)
        	->where(function($q) use ($now) {
        		$q->where('end_date', '>=', $now)
        			->orWhereNull('end_date');
        	})
        	->where(function($q) use ($now) {
        		$q->where('last_generated_date', '>', $now)
        			->orWhereNull('last_generated_date');
        	})
        	->get();
	}
}