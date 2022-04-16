<?php

use Illuminate\Database\Seeder;
use App\Tenant\Vendor\Vendor;
use App\Tenant\Expense\Expense;
use App\Tenant\Expense\Category;
use Faker\Factory;

class ExpensesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

    	$categories = Category::all();
    	$vendors = Vendor::all();

    	Expense::truncate();

        foreach(range(1, 40) as $i) {
        	$day = now()->subDays($i + mt_rand(0, 2));

        	$model = Expense::create([
        		'updated_at' => $day,
        		'created_at' => $day,
        		'payment_date' => $day->format('Y-m-d'),
        		'vendor_id' => $vendors->random()->id,
        		'category_id' => $categories->random()->id,
        		'method' => $faker->randomElement(['cheque', 'cash', 'bank_transfer']),
	            'sub_total' => mt_rand(1000, 40000),
	            'reference' => str_random(5).'/'.mt_rand(1000, 9000),
		    	'tax' => 5,
		    	'tax_total'  => 0,
		    	'tax_2' => 5,
		    	'tax_2_total' => 0,
		    	'note' => 'Expense note',
        		'number' => 'PD-'.mt_rand(10000, 90000).$i
        	]);

        	$model->tax_total = $model->sub_total * ($model->tax / 100);
        	$model->tax_2_total = $model->sub_total * ($model->tax_2 / 100);
        	$model->grand_total = $model->sub_total + $model->tax_total + $model->tax_2_total;

        	$model->save();
        }
    }
}
