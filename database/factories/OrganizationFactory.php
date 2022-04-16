<?php

use Faker\Generator as Faker;

$factory->define(App\Organization\Organization::class, function (Faker $faker) {
	$address =  $faker->streetAddress;
    return [
        'organization_category_id' => function () use ($faker) {
            $color = null;
        	do {
        		$color = $faker->colorName;
        	} while (App\Organization\Category::where('name', $color)->exists());

            return factory(App\Organization\Category::class)
                    ->create(['name' => $color])->id;
        },
        'number' => 'OR-'.$faker->unique()->numberBetween(100000, 900000),
        'name' => $faker->company,
        'phone' => $faker->phoneNumber,
        'fax' => $faker->phoneNumber,
        'email' => $faker->companyEmail,
        'website' => $faker->domainName,
        'primary_address' => $address,
        'other_address' => $address,
        'total_revenue' => $faker->numberBetween(100000, 900000),
        'amount_receivable' => $faker->numberBetween(0, 90000)
    ];
});


$factory->define(App\Organization\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName
    ];
});