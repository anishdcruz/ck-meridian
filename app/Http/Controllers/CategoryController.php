<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class CategoryController extends Controller {

	protected $withIndex = [];
	protected $withShow = [];
	protected $withEdit = [];

	protected $createForm = [];

	protected $typeaheadColumns = [];
	protected $typeaheadRequired = [];

	protected function response($arr)
	{
		return response()
			->json($arr);
	}

	public function search()
	{
        $collection = $this->model::search();

		return $this->response([
    		'collection' => $collection
    	]);
	}

	public function typeahead()
    {

        $collection = $this->model::typeahead(
        	$this->typeaheadColumns,
        	$this->typeaheadRequired
        );

        return [
            'results' => $collection
        ];
    }

	public function getIndex()
	{
		return $this->model::with($this->withIndex)->filter();
	}

	public function index()
    {
    	$this->authorize('settings', $this->title);
    	$collection = $this->getIndex();

    	return $this->response([
    		'collection' => $collection
    	]);
    }

    public function create()
    {
    	$this->authorize('settings', $this->title);
    	$form = $this->createForm();

    	return $this->response([
    		'form' => $form
    	]);
    }

    public function show($id)
    {
    	$this->authorize('settings', $this->title);
    	$model = $this->model::with($this->withShow)
    		->findOrFail($id);

    	return $this->response([
    		'model' => $model
    	]);
    }

    public function createRules()
    {
    	return ['name' => 'required|unique:team.'.$this->table.',name',];
    }

    public function inCreateTransaction($form)
    {
    	return $form;
    }

    public function store(Request $request)
    {
    	abort_in_demo();
    	$this->authorize('settings', $this->title);
    	$request->validate($this->createRules());

    	$form = $this->model::getModel();
    	$form->name = $request->name;
    	$form->save();

    	return $this->response([
    		'saved' => true,
    		'item' => $form
    	]);
    }

    public function edit($id)
    {
    	$this->authorize('settings', $this->title);
    	$form = $this->model::with($this->withEdit)
    		->findOrFail($id);

    	return $this->response([
    		'form' => $form
    	]);
    }

    public function updateRules($id)
    {
    	return [
            'name' => 'required|unique:team.'.$this->table.',name,'.$id.',id',
        ];
    }

    public function update($id, Request $request)
    {
    	abort_in_demo();
    	$this->authorize('settings', $this->title);
    	$request->validate($this->updateRules($id));

    	$form = $this->model::findOrFail($id);
    	$form->name = $request->name;
    	$form->save();

    	return $this->response([
    		'saved' => true,
    		'item' => $form
    	]);
    }

    public function confirmDestroy($model, $db)
    {
    	if(DB::connection('team')->table($this->parent)->where('category_id', $model->id)->count()) {
    	    return false;
    	}

    	return true;
    }

    public function destroy($id)
    {
    	abort_in_demo();
    	$this->authorize('settings', $this->title);
    	$found = $this->model::findOrFail($id);

    	if(!$this->confirmDestroy($found, new DB)) {
	    	return response()->json([
	        	'message' => __('app.cannot_delete'),
		        'errors' => []
		    ], 422);
    	}

    	$found->delete();

    	return $this->response([
    		'deleted' => true,
    		'id' => $id
    	]);
    }
}