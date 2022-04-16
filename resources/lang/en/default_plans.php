<?php

return [
	'p1' => [
    	'name' => 'Basic',
    	'slug' => 'basic',
    	'gateway_id' => 'basic_2019',
    	'time_period' => 'monthly',
    	'price' => 10,
    	'active' => 1,
        'limits' => [
        	[
        		"name" => "teams",
        	   	"max" => 1,
        	],
        	[
        	   	"name" => "users_per_team",
        		"max" => 1,
        	]
        ],
    	'list' => [
    		'1 Team',
    		'1 User per team',
    		'Unlimted Contacts',
    		'Unlimted Quotations',
    		'Unlimted Invoices',
    		'Recurring Invoices',
    		'Easy cancellation'
    	]
    ],
    'p2' => [
    	'name' => 'Standard',
    	'slug' => 'standard',
    	'gateway_id' => 'standard_2019',
    	'time_period' => 'monthly',
    	'price' => 25,
    	'active' => 1,
        'limits' => [
        	[
        		"name" => "teams",
        	   	"max" => 5,
        	],
        	[
        	   	"name" => "users_per_team",
        		"max" => 10,
        	]
        ],
    	'list' => [
    		'5 Team',
    		'10 User per team',
    		'Unlimted Contacts',
    		'Unlimted Quotations',
    		'Unlimted Invoices',
    		'Recurring Invoices',
    		'Easy cancellation'
    	]
    ],
    'p3' => [
    	'name' => 'Professional',
    	'slug' => 'professional',
    	'gateway_id' => 'professional_2019',
    	'time_period' => 'monthly',
    	'price' => 100,
    	'active' => 1,
        'limits' => [
        	[
        		"name" => "teams",
        	   	"max" => 10,
        	],
        	[
        	   	"name" => "users_per_team",
        		"max" => 25,
        	]
        ],
    	'list' => [
    		'10 Team',
    		'25 User per team',
    		'Unlimted Contacts',
    		'Unlimted Quotations',
    		'Unlimted Invoices',
    		'Recurring Invoices',
    		'Easy cancellation'
    	]
    ]
];