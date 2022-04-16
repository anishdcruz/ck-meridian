<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	$this->call(ContactsTableSeeder::class);
    	$this->call(ItemsTableSeeder::class);
    	$this->call(QuotationsTableSeeder::class);
    	$this->call(InvoicesTableSeeder::class);
    	$this->call(PaymentsTableSeeder::class);
    	$this->call(RefundsTableSeeder::class);

    	$this->call(VendorsTableSeeder::class);
    	$this->call(PurchaseOrderTableSeeder::class);
    	$this->call(PaymentsMadeTableSeeder::class);
    	$this->call(ExpensesTableSeeder::class);
    }
}
