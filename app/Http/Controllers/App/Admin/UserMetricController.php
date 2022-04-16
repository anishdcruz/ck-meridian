<?php

namespace App\Http\Controllers\App\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin\UserMetric;
use DB;

class UserMetricController extends Controller
{
	protected function allowedResources()
	{
		return implode(',', [
			'users',
			'subscriptions',
			'teams',
			'plans',
			'faqs',
			'admins',
			'currencies'
		]);
	}

    public function store(Request $request)
    {
    	abort_in_demo();
    	$data = $request->validate([
    	    'card_label' => 'required|string',
    	    'filter_id' => 'sometimes|required',
    	    'metric_type' => 'required|in:value,chart', // metric_type,
            'value_type' => 'required_if:metric_type,value',
            'value_field' => 'nullable',
            'chart_type' => 'required',
            'color' => 'sometimes',
            'x_axis_type' => 'required_if:metric_type,chart',
            'y_axis_type' => 'required_if:metric_type,chart',
            'x_axis_field' => 'required_if:metric_type,chart',
            'y_axis_field' => 'nullable'
    	]);

        $last = UserMetric::select(DB::raw('MAX(y) as last_y'))
            ->where('user_id', admin_id())
            ->first();

        $x = 0;
        $y = 0;
        $h = $request->metric_type  == 'chart' ? 8 : 4;
        $w = 1;

        if($last) {
            $y = $last->last_y + $h;
        }

    	$re = new UserMetric;
    	$re->fill($data);
    	$re->user_id = admin_id();
        $re->i = str_random(8);
        $re->x = $x;
        $re->y = $y;
        $re->w = $w;
        $re->h = $h;
    	$re->save();

    	cache()->clear('av'.admin_id().'t');
    	return [
    	    'saved' => true
    	];
    }

    public function update($id,Request $request)
    {
    	abort_in_demo();
    	$data = $request->validate([
    	    'card_label' => 'required|string',
    	    'filter_id' => 'sometimes|required',
    	    'metric_type' => 'required|in:value,chart', // metric_type,
            'value_type' => 'required_if:metric_type,value',
            'value_field' => 'nullable',
            'chart_type' => 'required',
            'color' => 'sometimes',
            'x_axis_type' => 'required_if:metric_type,chart',
            'y_axis_type' => 'required_if:metric_type,chart',
            'x_axis_field' => 'required_if:metric_type,chart',
            'y_axis_field' => 'nullable'
    	]);

        $re = UserMetric::where('user_id', admin_id())
        	->where('id', $id)
            ->firstOrFail();

    	$re->fill($data);
    	$re->save();
    	cache()->clear('av'.admin_id().'t');
    	return [
    	    'saved' => true
    	];
    }

    public function updateAll(Request $request)
    {
    	abort_in_demo();
        $id = admin_id();

        $request->validate([
            'metrics.*.id' => 'required|integer|exists:user_metrics,id,user_id,'.$id,
            'metrics.*.x' => 'required|integer',
            'metrics.*.y' => 'required|integer',
            'metrics.*.w' => 'required|integer',
            'metrics.*.h' => 'required|integer',
        ]);

        foreach ($request->metrics as $metric) {
            UserMetric::where('id', $metric['id'])
            	->where('user_id', admin_id())
                ->update([
                    'x' => $metric['x'],
                    'y' => $metric['y'],
                    'w' => $metric['w'],
                    'h' => $metric['h']
                ]);
        }
        cache()->clear('av'.admin_id().'t');
        return [
            'saved' => true
        ];
    }

    public function destroy($id)
    {
    	abort_in_demo();
        $found = UserMetric::where('user_id', admin_id())
            ->where('id', $id)
            ->firstOrFail();


        $found->delete();
        cache()->clear('av'.admin_id().'t');
        return [
            'deleted' => true
        ];
    }
}
