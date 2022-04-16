<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\RecurringInvoiceDispatcher;
use App\Services\RecurringExportDispatcher;
use Schema;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\LangToJsCommand::class,
        Commands\Installer::class,
        Commands\DemoSetupCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    	$found = cache()->remember('check_tables', 5600, function() {
    		$invoice = Schema::hasTable('team_recurring_invoices');
    		$export = Schema::hasTable('team_recurring_exports');
    		return $invoice || $export;
    	});
    	if(!$found) {
    		return ;
    	}

    	// new RecurringInvoiceDispatcher($schedule);
    	// new RecurringExportDispatcher($schedule);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
