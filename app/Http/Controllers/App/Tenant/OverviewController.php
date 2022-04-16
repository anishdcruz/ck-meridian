<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tenant\UserMetric;

class OverviewController extends Controller
{
	public function index()
	{
		// save metrics for 5 minutes
		$metrics = cache()->remember('ov.'.auth()->id().'t'.get_team()->id, 300, function() {
			$metrics = UserMetric::with('filter')->where('user_id', auth()->id())
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
