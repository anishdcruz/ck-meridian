<?php

namespace App\Http\Controllers\App\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin\UserMetric;
use DB;
class OverviewController extends Controller
{
	public function index()
	{
		// save metrics for 5 minutes
		$id = admin_id();

		$metrics = cache()->remember('av.'.$id.'t', config('meridian.metric_cache'), function() use ($id) {
			$metrics = UserMetric::with('filter')->where('user_id', $id)
				->get();

			$metrics = $metrics->map(function($item) {
				$filter = $item->filter;
				if($filter) {
					$model = $filter->model();
					$values = $model::metrics($filter, $item);

					$res = $item;
					$res->resource = $item->filter->resource;
					$temp = $item->filter;
					unset($item->filter);
					$res->filter = [
						'id' => $temp->id,
						'name' => $temp->name
					];
					$res->values = $values;
					return $res;
				} else {
					$res = $item;
					$res->card_label = $item->card_label.' [Filter Not Found!]';
					$res->resource = null;
					$res->values = [];
					return $res;
				}
			});

			return $metrics;
		});

	    return [
			'metrics' => $metrics
		];
	}
}
