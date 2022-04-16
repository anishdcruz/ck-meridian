<?php

use Illuminate\Database\Seeder;
use App\Tenant\Contact\Contact;
use App\Tenant\Contact\Category;
use Faker\Factory;

class ContactsTableSeeder extends Seeder
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
    	Contact::truncate();

        foreach(range(1, 40) as $i) {
        	Contact::create([
        		'name' => $faker->name,
        		'organization' => mt_rand(0, 1) ? $faker->company : null,
        		'category_id' => $categories->random()->id,
        		'email' => $faker->email,
        		'title' => null,
        		'department' => null,
        		'mobile' => $faker->phoneNumber,
        		'phone' => null,
        		'fax' => null,
        		'website' => null,
        		'primary_address' => $faker->address,
        		'other_address' => mt_rand(0, 1) ? $faker->address : null,
        		'tax_id' => str_random(3).mt_rand(1000000, 9000000).$i,
        		'number' => 'CT-'.mt_rand(10000, 90000).$i,
        		'updated_at' => now()->subDays($i + mt_rand(0, 2)),
        		'created_at' => now()->subDays($i + mt_rand(0, 2))
        	]);
        }
    }
}
