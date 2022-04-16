<?php

use Illuminate\Database\Seeder;
use App\Tenant\Invoice\Invoice;
use App\Tenant\Payment\Payment;
use Faker\Factory;

class PaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $invoices = Invoice::where('status_id', 3)->get();

        Payment::truncate();

        foreach($invoices as  $invoice) {

        	$day = now()->parse($invoice->issue_date)
        		->addDays(mt_rand(2, 5));

        	$payment = new Payment([
        		'number' => 'PY-'.mt_rand(10000, 90000),
        		'updated_at' => $day,
        		'created_at' => $day,
        		'invoice_id' => $invoice->id,
        		'method' => $faker->randomElement(['cheque', 'cash', 'bank_transfer']),
	            'contact_id' => $invoice->contact_id,
	            'payment_date' => $day->format('Y-m-d'),
	            'note' => str_random(5).'/'.mt_rand(1000, 9000),
		    	'amount_received' => $invoice->grand_total,
		    	'transaction_fees' => 0,
		    	'net_amount' => $invoice->grand_total
        	]);

        	$payment->save();

        	$amount = $invoice->amount_paid + $payment->amount_received;
            $invoice->amount_paid = $amount;
            $invoice->save();

            $contact = $payment->contact;
            $contact->total_revenue += $payment->amount_received;
            $contact->save();
        }
    }
}
