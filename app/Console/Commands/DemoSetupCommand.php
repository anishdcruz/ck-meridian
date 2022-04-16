<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Team;
use TeamManager;
use App\Plan;
use App\Repository\UserRepository;
use App\Repository\TeamRepository;
use DB;
use App\Facades\TeamConfig;
use File;
use Exception;

class DemoSetupCommand extends Command
{
	protected $user;
	protected $team;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meridian:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demo installation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserRepository $user, TeamRepository $team)
    {
        parent::__construct();

        $this->user = $user;
        $this->team = $team;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	if(inSAASMode()) {
    		$this->seedUsers();
    		// create demo user and team
    		$plan = Plan::where('gateway_id', 'standard_2019')->first();
    		$team = $this->defaultUserTeam($plan);
    	} else {
    		$team = Team::first();
    	}

		$this->setTeam($team);

		$team->is_setup = true;
		$team->save();
    }

    public function seedUsers()
    {
    	 $this->call('db:seed', array_filter([
	        '--database' => 'mysql',
	        '--class' => 'SubscriptionsTableSeeder',
	        '--force' => true,
	    ]));
    }

   	public function defaultUserTeam($plan)
    {
    	$email = 'user@meridian.test';
    	$name = 'Demo User';
    	$team = 'Demo Team';
    	$password = 'password';

    	$data = [
            'name' => $name,
            'email' => $email,
            'company_name' => $team,
            'password' => $password,
        ];

    	// create user
    	$user = $this->user->createUserFromRegistration(
    	    $data,
    	    false,
    	    $plan
    	);

    	// create dummy subscription
    	$user->stripe_id = 'cus_st';
    	$user->card_brand = 'visa';
    	$user->card_last_four = '4242';
    	$user->save();

    	DB::table('subscriptions')
    		->insert([
    			'user_id' => $user->id,
    			'name' => 'main',
    			'stripe_id' => 'cus_st',
    			'stripe_plan' => $plan->gateway_id,
    			'quantity' => 1,
    			'created_at' => now(),
    			'updated_at' => now()
    		]);

    	// create team
    	$team = $this->team->create($user, [
    	    'name' => $data['company_name']
    	]);

    	return $team;
    }

    protected function setTeam($team)
    {
		TeamManager::setTeam($team);

		// set logo, address, etc
		$this->setTeamDefaults();

	    // seed to demo database
	    $this->call('db:seed', array_filter([
	        '--database' => 'team',
	        '--class' => 'DatabaseSeeder',
	        '--force' => true,
	    ]));
    }

    protected function setTeamDefaults()
    {
    	// copy meridian logo
    	$old_path = database_path('seeds/demo-logo-1.png');
    	$new_path = storage_path('app/public/logos/demo-logo-1.png');

    	try {
    		File::makeDirectory(storage_path('app/public/logos'));
    	} catch (Exception $e) {

    	}

    	$move = File::copy($old_path, $new_path);

    	TeamConfig::setMany([
    		'company_logo' => 'logos/demo-logo-1.png',
    		'company_name' => 'Meridian Demo',
			'company_address' => '70558 Hilario Common',
			'company_email' => 'demo@meridian.test',
			'company_telephone' => '23131230, 2323222',
			'registration_label' => 'VAT',
			'tax_enable' => 1,
			'tax_label' => 'VAT',
			'company_tax_id' => 'B23071Q8801'
		]);
    }
}
