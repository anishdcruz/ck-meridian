<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Team;
use DB;

class DeleteTeam implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $team;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	// delete jobs
    	// delete recurring_invoice
    	DB::table('team_recurring_invoices')
    		->where('team_id', $this->team->id)
    		->delete();

    	// delete recurring exports
    	DB::table('users')
    		->where('current_team_id', $this->team->id)
    		->update(['current_team_id' => null]);

    	// delete stripe accounts
    	DB::table('stripe_connect_accounts')
    		->where('team_id', $this->team->id)
    		->delete();

    	// delete roles
    	DB::table('roles')
    		->where('team_id', $this->team->id)
    		->delete();

    	// delete invitations
    	DB::table('invitations')
    		->where('team_id', $this->team->id)
    		->delete();

    	// delete user roles
    	DB::table('user_teams')
    		->where('team_id', $this->team->id)
    		->delete();

        // drop database
    	$create = DB::statement('drop database ' . $this->team->database);

        // delete teams
        $this->team->delete();
    }
}
