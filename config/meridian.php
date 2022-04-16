<?php

return [
	// standalone or saas
	'app_mode' => env('APP_MODE', 'saas'),
	'app_prefix' => env('APP_PREFIX', ''),
	'admin_prefix' => env('ADMIN_PREFIX', ''),
	'enable_payment' => env('STRIPE_PAYMENT_IN_STANDALONE_MODE', false),
	'max_logo_size' => 2000,
	'per_page' => 10,
	'metric_cache' => env('METRIC_CACHE', 300),
	'in_demo' => env('IN_DEMO', false),
	'date_formats' => [
		'd-m-Y',
		'Y-m-d',
		'd-M-Y',
		'Y-M-d',
		'd/m/Y',
		'Y/m/d'
	],
	'pdf' => [
		'max_row_per_file' => 23,
		'char_per_line' => 38,
		'disable_empty_row_border' => false
	],
	'langs' => [
		'en' => 'English',
		// 'fr' => 'French'
	],
	'features' => [
		[
			'title' => 'Clean UI',
			'description' => 'Simple and easy to use solution',
			'image' => 'img/undraw_done_a34v.png'
		],
		[
			'title' => 'Multiple Teams',
			'description' => 'Create teams to manage multiple organizations',
			'image' => 'img/undraw_team_ih79.png'
		],
		[
			'title' => 'Roles and Permissions',
			'description' => 'Ability to assign roles to members of team',
			'image' => 'img/undraw_security_o890.png'
		],
		[
			'title' => 'Dynamic Dashboards',
			'description' => 'Customizable dashboard based on member roles',
			'image' => 'img/undraw_data_trends_b0wg.png'
		],
		[
			'title' => 'Data Exports',
			'description' => 'Easily export data to spreadsheets',
			'image' => 'img/f-spreadsheets.png'
		],
		[
			'title' => 'Advanced Filters',
			'description' => 'Using multiple filters to drill down data',
			'image' => 'img/undraw_filter_4kje.png'
		],
	],
	'features_in_detail' => [
		[
			'title' => 'Dynamic Dashboard',
			'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
			'image' => 'img/features/dashboard.png'
		],
		[
			'title' => 'Multiple teams',
			'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
			'image' => 'img/features/team.png'
		],
		[
			'title' => 'A wide range of team settings',
			'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
			'image' => 'img/features/team-setting.png'
		],
		[
			'title' => 'Stripe Connect for Tenants',
			'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
			'image' => 'img/features/stripe-connect.png'
		],
		[
			'title' => 'Roles and Permissions',
			'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
			'image' => 'img/features/role-and-perm.png'
		]
	],
	'customers' => [
		[
			'active' => true,
			'name' => 'Person Name',
			'company' => 'Company Name',
			'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.',
			'image' => 'img/test-1.png'
		],
		[
			'name' => 'Person Name 2',
			'company' => 'Company Name',
			'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.',
			'image' => 'img/test-2.png'
		],
		[
			'name' => 'Person Name 3',
			'company' => 'Company Name',
			'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.',
			'image' => 'img/test-3.png'
		]
	]
];