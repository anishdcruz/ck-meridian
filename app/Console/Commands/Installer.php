<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Faq;
use App\Plan;
use App\Admin;
use App\Repository\UserRepository;
use App\Repository\TeamRepository;
use DB;
use TeamManager;
use Exception;
use File;
use App\Admin\Filter;
use App\Admin\UserMetric;

class Installer extends Command
{
	protected $user;
	protected $team;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meridian:install {--demo} {--reset} {--standalone} {--name=} {--email=} {--password=} {--team=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fresh installation of Meridian';

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
        // key generate
        if(!$this->option('reset')) {
            $this->call('key:generate', ['--force' => true]);
        }

        $this->call('route:clear');
        $this->call('cache:clear');

        try {
        	$this->call('storage:link');
        } catch (Exception $e) {

        }

        // migrate
        $this->call('migrate', ['--force' => true]);


        if(!$this->option('standalone')) {
        	// default faqs
        	$this->setupDefaultFAQs();

        	// default plans
        	$this->setupDefaultPlans();
        } else {
        	// setup standalone mode
        	$plan = $this->setupStandalonePlan();
        	$this->defaultUserTeam($plan);
        	DB::purge('team');
        }

        // default admins
		$this->setupDefaultAdmins();

		$this->installationCompleted();

    }

    public function installationCompleted()
    {
    	$this->info("*****");
    	$this->info("Installation completed");
    	$this->info('Mode: '. strtoupper(config('meridian.app_mode')));
    	$this->info('Application: '. getMeridianURL('app'));
    	$this->info('Admin: '. getMeridianURL('admin'));
    	$this->info("*****");
    }

    public function defaultUserTeam($plan)
    {
    	$email = $this->option('email') ?? 'user@meridian.test';
    	$name = $this->option('name') ?? 'Default User';
    	$team = $this->option('team') ?? 'Default Team';
    	$password = $this->option('password') ?? 'password';

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
    			'stripe_plan' => 'standalone',
    			'quantity' => 1,
    			'created_at' => now(),
    			'updated_at' => now()
    		]);

    	// create team
    	$team = $this->team->create($user, [
    	    'name' => $data['company_name']
    	]);
    }

    public function setupStandalonePlan()
    {
    	return Plan::create([
	    	'name' => 'Standalone',
	    	'slug' => 'standalone',
	    	'gateway_id' => 'standalone',
	    	'time_period' => 'monthly',
	    	'price' => 1000000,
	    	'active' => 1,
	        'limits' => [
	        	[
	        		"name" => "teams",
	        	   	"max" => 100,
	        	],
	        	[
	        	   	"name" => "users_per_team",
	        		"max" => 500,
	        	]
	        ],
	    	'list' => [
	    		'standalone mode'
	    	]
	    ]);
    }


    public function setupDefaultFAQs()
    {
    	$faqs = [
    		['question' => __('default_faqs.q1'), 'answer' => __('default_faqs.a1'), 'show_on_pricing' => 1],
    		['question' => __('default_faqs.q2'), 'answer' => __('default_faqs.a2'), 'show_on_pricing' => 1] ,
    		['question' => __('default_faqs.q3'), 'answer' => __('default_faqs.a3'), 'show_on_pricing' => 1]
    	];

    	foreach($faqs as $faq) {
    		Faq::create($faq);
    	}
    }

    public function setupDefaultPlans()
    {
    	$plans = [
    		__('default_plans.p1'),
    		__('default_plans.p2'),
    		__('default_plans.p3')
    	];


    	foreach($plans as $plan) {
    		Plan::create($plan);
    	}
    }

    public function setupDefaultAdmins()
    {
    	$admin = [
    		'name' => 'Administrator',
    		'email' => 'admin@meridian.test',
    		'password' => bcrypt('password')
    	];

    	$u = Admin::create($admin);

    	$file = File::get(base_path('database/seeds/admin_default_filters.json'));
		$json = json_decode($file, 1);

		foreach ($json as $saved) {
			Filter::create([
				'user_id' => $u->id,
				'name' => $saved['name'],
				'shared_with' => $saved['shared_with'],
				'params' => json_encode($saved['params']),
				'resource' => $saved['resource'],
				'filter_match' => $saved['filter_match']
			]);
		}

		// add default dashboard
		$file = File::get(base_path('database/seeds/admin_default_metrics.json'));
		$json = json_decode($file, 1);

		foreach($json as $key => $d) {
			$found = Filter::where('name', $d['filter']['name'])
				->first();

			if($found) {
				$re = new UserMetric;
				$re->fill($d);
				$re->filter_id = $found->id;
				$re->user_id = $u->id;
				$re->save();
			}
		}
    }
}
