<?php

use Illuminate\Database\Seeder;
use App\Tenant\Payment\Payment;
use App\Tenant\Refund\Refund;
use Faker\Factory;

class RefundsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $payments = Payment::inRandomOrder()->limit(7)->get();
        Refund::truncate();

        foreach($payments as  $payment) {

        	$day = now()->parse($payment->issue_date)
        		->addDays(mt_rand(2, 5));

        	$ref = new Refund([
        		'number' => 'PY-'.mt_rand(10000, 90000),
        		'updated_at' => $day,
        		'created_at' => $day,
        		'payment_id' => $payment->id,
        		// 'method' => $faker->randomElement(['cheque', 'cash', 'bank_transfer']),
	            'contact_id' => $payment->contact_id,
	            'refund_date' => $day->format('Y-m-d'),
	            'reason' => 'customer_requested',
		    	'description' => 'description'
        	]);
        	$ref->amount =  $payment->amount_received;
        	$ref->save();

        	$payment->amount_refunded = $ref->amount;
            $payment->amount_received = 0;
            $payment->net_amount = 0;
            $payment->save();
        }
    }
}
