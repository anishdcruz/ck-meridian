<?php

use Illuminate\Database\Seeder;
use App\Tenant\Vendor\Vendor;
use App\Tenant\PurchaseOrder\PurchaseOrder;
use App\Tenant\PurchaseOrder\Line;
use App\Tenant\PurchaseOrder\Status;
use App\Tenant\Term;
use App\Tenant\Item\Item;
use Faker\Factory;

class PurchaseOrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $vendors = Vendor::all();
        $terms = Term::where('label', 'like', 'po%')->get();
        $status = Status::get();
        $items = Item::get();

        PurchaseOrder::truncate();
        Line::truncate();

        foreach(range(1, 50) as  $i) {

        	$day = now()->subDays($i + mt_rand(0, 2));
        	$model = new PurchaseOrder([
        		'number' => 'PO-'.mt_rand(10000, 90000).$i,
        		'updated_at' => $day,
        		'created_at' => $day,

	            'vendor_id' => $vendors->random()->id,
	            'issue_date' => $day->format('Y-m-d'),
	            'reference' => str_random(5).'/'.mt_rand(1000, 9000),
	            'discount' => 0,
		    	'tax' => 5,
		    	'tax_total'  => 0,
		    	'tax_2' => 5,
		    	'tax_2_total' => 0,
		    	'term_id' => $terms->random()->id,
		    	'notes' => null,
		    	'sub_total' => 0,
		    	'status_id' =>$status->random()->id
        	]);

        	$lines = collect();
        	foreach($items->random(mt_rand(3, 8)) as $item) {
        		$lines->push(new Line([
        			'item_id' => $item->id,
        			'qty' => mt_rand(1, 5),
        			'unit_price' => $item->unit_price
        		]));
        	}

        	$model->sub_total = $lines->sum(function($item) {
        		return $item->qty * $item->unit_price;
        	});
        	$model->tax_total = $model->sub_total * ($model->tax / 100);
        	$model->tax_2_total = $model->sub_total * ($model->tax_2 / 100);
        	$model->grand_total = $model->sub_total + $model->tax_total + $model->tax_2_total;

        	$model->save();
        	$model->lines()->saveMany($lines);
        }
    }
}
