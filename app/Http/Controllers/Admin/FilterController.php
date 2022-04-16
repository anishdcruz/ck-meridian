<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin\Filter;
use DB;

class FilterController extends Controller
{
	protected function allowedResources()
	{
		return implode(',', [
			'users',
			'subscriptions',
            'plans',
            'teams',
            'faqs',
            'admins',
            'currencies'
		]);
	}

    public function search(Request $request)
    {
        $request->validate([
            'resource' => 'required|in:'.$this->allowedResources()
        ]);

        $collection = Filter::select('id', 'name', DB::Raw( 'name'. ' as value'))
            ->where('resource', request('resource'))
            ->where(function($query) {
                $query->where('user_id', admin_id())
                ->orWhere('shared_with', 'team');
            })
            ->when(request('query'), function($q) {
                $q->where('name', 'like', '%'.request('query').'%');
            })
            ->orderBy('name')
            ->get();

        return [
            'collection' => $collection
        ];
    }

	public function index(Request $request)
	{
		$request->validate([
    		'resource' => 'required|in:'.$this->allowedResources()
    	]);

    	$filters = Filter::where('resource', $request->resource)
    		->where(function($query) {
                $query->where('user_id', admin_id())
                ->orWhere('shared_with', 'team');
            })
            ->orderBy('name')
    		->get();

    	return [
    		'collection' => $filters
    	];
	}

    public function store(Request $request)
    {
    	abort_in_demo();
    	$request->validate([
    		'name' => 'required|string',
    		'shared_with' => 'required|in:team,me',
    		'params' => 'required|array',
    		'resource' => 'required|in:'.$this->allowedResources()
    	]);

    	$filter = new Filter($request->only([
    		'name', 'shared_with', 'resource', 'filter_match'
    	]));

    	$filter->params = json_encode($request->params);

    	$filter->user_id = admin_id();
    	$filter->save();

    	return [
    		'saved' => true,
    		'id' => $filter->id
    	];
    }

    public function update($id, Request $request)
    {
    	abort_in_demo();
    	$request->validate([
    		'name' => 'required|string',
    		'shared_with' => 'required|in:team,me',
    		'params' => 'required|array',
    		'resource' => 'required|in:'.$this->allowedResources()
    	]);

    	$filter = Filter::findOrFail($id);
    	if($filter->user_id != admin_id()) {
    		return [
	    		'saved' => true
	    	];
    	}
    	$filter->fill($request->only([
    		'name', 'shared_with', 'resource'
    	]));

    	$filter->params = json_encode($request->params);
    	$filter->save();

    	return [
    		'saved' => true
    	];
    }

    public function destroy($id)
    {
    	abort_in_demo();
    	$filter = Filter::findOrFail($id);

    	if($filter->user_id != admin_id()) {
    		abort(403);
    	}

    	$filter->delete();

    	return [
    		'saved' => true
    	];
    }
}
