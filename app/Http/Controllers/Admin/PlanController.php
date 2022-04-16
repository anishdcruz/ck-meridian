<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Plan;
use DB;

class PlanController extends AdminController
{
    protected $title = 'plans';
	protected $model = Plan::class;

	public function createForm()
	{
		$form = [
			'name' => '',
			'gateway_id' => '',
			'time_period' => '',
			'price' => '',
			'active' => 0,
			'list' => [''],
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
			'notes' => ''
		];

		return $form;
	}

	public function confirmDestroy($model, $db)
	{
		// check active subscription with planid
		$count = $db::table('subscriptions')
			->where('stripe_plan', $model->gateway_id)
			->where(function($q) {
				$q->whereNull('ends_at')
					->orWhere('ends_at', '>', now());
			})
			->where(function($q) {
				$q->whereNull('trial_ends_at')
					->orWhere('trial_ends_at', '>', now());
			})
			->count();
		return !$count > 0;
	}
}
