<?php

use Illuminate\Database\Seeder;
use App\User;
use Faker\Factory;
use App\Plan;

class SubscriptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        User::truncate();
        $plans = Plan::where('active', 1)->get();

        foreach(range(1, 100) as $i) {
        	$user = new User([
        		'name' => $faker->name,
        		'email' => $faker->safeEmail,
        		'password' => bcrypt('p@ssw5rd123123!'),
        	]);
        	$day = now()->subDays($i + mt_rand(0, 2));
        	$user->created_at = $day;
        	$user->updated_at = $day;
        	$user->stripe_id = 'cus_st';
        	$user->card_brand = 'visa';
        	$user->card_last_four = '4242';
        	$user->save();
        	$day = now()->subDays($i + mt_rand(0, 2));
        	if(mt_rand(0, 1)) {
	        	if(mt_rand(0, 1)) {
	        		// ended
	        		DB::table('subscriptions')
		    		->insert([
		    			'user_id' => $user->id,
		    			'name' => 'main',
		    			'stripe_id' => 'cus_st'.$user->id,
		    			'stripe_plan' => $plans->random()->gateway_id,
		    			'quantity' => 1,
		    			'ends_at' => now()->addDays(mt_rand(1, 20)),
		    			'created_at' => $day,
		    			'updated_at' => $day
		    		]);
	        	} else {
	        		DB::table('subscriptions')
		    		->insert([
		    			'user_id' => $user->id,
		    			'name' => 'main',
		    			'stripe_id' => 'cus_st'.$user->id,
		    			'stripe_plan' => $plans->random()->gateway_id,
		    			'quantity' => 1,
		    			'created_at' => $day,
		    			'updated_at' => $day
		    		]);
	        	}
        	}
        }
    }
}
