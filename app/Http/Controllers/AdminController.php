<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class AdminController extends Controller {

	protected $withIndex = [];
	protected $withShow = [];
	protected $withEdit = [];

	protected $createForm = [];

	protected $typeaheadColumns = [];
	protected $typeaheadRequired = [];

	protected $autoNumberField = 'number';

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
    	$collection = $this->getIndex();

    	return $this->response([
    		'collection' => $collection
    	]);
    }

    public function create()
    {
    	$form = $this->createForm();

    	return $this->response([
    		'form' => $form
    	]);
    }

    public function show($id)
    {
    	$model = $this->model::with($this->withShow)
    		->findOrFail($id);

    	return $this->response([
    		'model' => $model
    	]);
    }

    public function createRules()
    {
    	return [];
    }

    public function inCreateTransaction($form)
    {
    	return $form;
    }

    public function afterCreate($form)
    {
    	return $form;
    }

    public function store(Request $request)
    {
    	abort_in_demo();
    	$request->validate($this->createRules());

    	$form = $this->model::getModel();
    	$form->fill($request->all());

    	$form = DB::transaction(function() use ($form) {
    	    // $c = counter($this->counter);
    	    // $form->fill([$this->autoNumberField => $c->number]);
    	    // $form->{$this->autoNumberField} = $c->number;
    	    $form = $this->inCreateTransaction($form);
    	    $form->save();
    	    // $c->increment('value');
    	    $form = $this->afterCreate($form);
    	    return $form;
    	});

    	return $this->response([
    		'saved' => true,
    		'id' => $form->id
    	]);
    }

    public function edit($id)
    {
    	$form = $this->model::with($this->withEdit)
    		->findOrFail($id);

    	return $this->response([
    		'form' => $form
    	]);
    }

    public function updateRules($id)
    {
    	return [];
    }

    public function update($id, Request $request)
    {
    	abort_in_demo();
    	$request->validate($this->updateRules($id));

    	$form = $this->model::findOrFail($id);
    	$form->fill($request->all());
    	$form->save();

    	return $this->response([
    		'saved' => true,
    		'id' => $form->id
    	]);
    }

    public function confirmDestroy($model, $db)
    {
    	return true;
    }

    public function destroy($id)
    {
    	abort_in_demo();
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

    public function export(Request $request)
    {
    	abort_in_demo();
        return Excel::download($this->setExport($request), __('app.'.$this->title).'.xlsx');
    }
}