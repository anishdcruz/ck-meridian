<?php

use Illuminate\Database\Seeder;
use App\Tenant\Item\Item;
use App\Tenant\Item\Category;
use App\Tenant\Item\Uom;
use Faker\Factory;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = collect([
            'IP 500 V2 Control Unit',
            'IP500 Digital Station 30',
            'IP500 Digital Station 30A',
            'IP500 Digital Station 30B',
            'IP500 Compact Rack Mount Kit',
            'IP500 Rack Mounting Kit',
            'IP500 Digital Station 16',
            'IP500 Digital Station 16A',
            'IP500 Digital Station 16B',
            'IP500 V2 Combination Card ATM',
            'IP500V2 VCM32 V2',
            'IP500 Phone 2 Card - 2 POT ports',
            'IP500 Phone 8 Card - 8 POT ports',
            'IP500 Digital Station 8 Card - 8 DS ports',
            'IP500 V2 Norstar TCM8 Card - 8 Norstar ports',
            'IP500 ETR Extension Card - 6 ETR ports',
            'IP500 V2 Analog Trunk 4 Card - 4 lines',
            'IP500 Single Universal T1/PRI Card',
            'IP500 Dual Universal T1/PRI Card',
            'IP500 4-Port Expansion Card',
            '9601 SIP Phone',
            '9608 IP Phone',
            '9611G IP Phone',
            '9621G IP phone',
            '9630G IP Phone',
            '9641G IP Phone',
            '9670G IP Phone',
            'SBM24 Expansion Module',
            'Gigabit Ethernet Adaptor',
            '1608 IP Phone'
        ]);

        $faker = Factory::create();

    	$categories = Category::all();
    	$uoms = Uom::all();

    	Item::truncate();

        foreach(range(1, 40) as $i) {
        	Item::create([
        		'description' => $products->random(),
        		'category_id' => $categories->random()->id,
        		'uom_id' => $uoms->random()->id,
        		'unit_price' => mt_rand(100, 9000),
        		'code' => 'PD-'.mt_rand(10000, 90000).$i,
        		'updated_at' => now()->subDays($i + mt_rand(0, 2)),
        		'created_at' => now()->subDays($i + mt_rand(0, 2))
        	]);
        }
    }
}
