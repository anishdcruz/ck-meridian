<?php

namespace App\Services;

use Illuminate\Console\Scheduling\Schedule;
use App\TeamRecurringExport;
use App\Jobs\CreateExport;
use Schema;

class RecurringExportDispatcher {

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
		$exports = $this->getExports();
		foreach($exports as $export) {

			if($export->frequency == 'daily') {
				$this->schedule->job(new CreateExport($export))
			    ->dailyAt($export->send_at)
			    ->timezone('UTC')
			    ->appendOutputTo($this->filePath);
			} else {
				$frequency = $export->frequency.'On';
				$this->schedule->job(new CreateExport($export))
			    ->$frequency($export->send_on, $export->send_at)
			    ->timezone('UTC')
			    ->appendOutputTo($this->filePath);
			}

		}
	}

	protected function getExports()
	{
        $now = now()->setTimezone('UTC')->format('Y-m-d');

        return $exports = TeamRecurringExport::where(function($q) use ($now) {
        		$q->where('last_generated_date', '>', $now)
        			->orWhereNull('last_generated_date');
        	})
        	->get();
	}
}