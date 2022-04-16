<?php

use Illuminate\Database\Seeder;
use App\Tenant\PaymentMade\PaymentMade;
use App\Tenant\PurchaseOrder\PurchaseOrder;
use Faker\Factory;

class PaymentsMadeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $pos = PurchaseOrder::where('status_id', 3)->get();

        PaymentMade::truncate();

        foreach($pos as  $purchase_order) {

        	$day = now()->parse($purchase_order->issue_date)
        		->addDays(mt_rand(1, 3));

        	$payment = new PaymentMade([
        		'number' => 'PM-'.mt_rand(10000, 90000),
        		'updated_at' => $day,
        		'created_at' => $day,
        		'purchase_order_id' => $purchase_order->id,
        		'method' => $faker->randomElement(['cheque', 'cash', 'bank_transfer']),
	            'vendor_id' => $purchase_order->vendor_id,
	            'payment_date' => $day->format('Y-m-d'),
	            'note' => str_random(5).'/'.mt_rand(1000, 9000),
		    	'amount_paid' => $purchase_order->grand_total,
		    	'transaction_fees' => 0,
		    	'net_amount' => $purchase_order->grand_total
        	]);

        	$payment->save();

        	$amount = $purchase_order->amount_paid + $payment->amount_paid;
            $purchase_order->amount_paid = $amount;
            $purchase_order->save();

            $vendor = $payment->vendor;
            $vendor->total_paid += $payment->amount_paid;
            $vendor->save();
        }
    }
}
