<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Team;
use App\Subscription;
use App\Admin;
use App\Plan;
use App\Faq;
use App\Currency;

class Filter extends Model
{
	protected $connection = 'mysql';
    protected $table = 'filters';

	protected $fillable = [
		'name', 'shared_with', 'params', 'user_id', 'resource', 'filter_match'
	];

	public function getParamsAttribute()
	{
		return json_decode($this->attributes['params'], 1);
	}

	public function model()
	{
		$model = $this->findModal($this->attributes['resource']);
		return $model::getModel();
	}

	public function findModal($resource)
	{
		$models = [
			'users' => User::class,
			'subscriptions' => Subscription::class,
            'plans' => Plan::class,
            'teams' => Team::class,
            'faqs' => Faq::class,
            'admins' => Admin::class,
            'currencies' => Currency::class
		];

		return $models[$resource];
	}

	public function getParams()
	{
		$f = [];

		foreach ($this->getParamsAttribute() as $filter) {
			$temp = [
				'column' => array_get($filter, 'column.name'),
				'operator' => array_get($filter, 'operator.name')
			];

			if(isset($filter['query_1']) && is_array($filter['query_1'])) {
				$list = array_map(function($i) {
					return $i['value'];
				}, $filter['query_1']);

				$temp['query_1'] = implode(',,', $list);
			} else {
				$temp['query_1'] = $filter['query_1'];
			}
			$temp['query_2'] = $filter['query_2'];
			array_push($f, $temp);
		}

		return $f;
	}
}
