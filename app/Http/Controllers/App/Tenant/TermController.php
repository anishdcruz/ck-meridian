<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tenant\Term;

class TermController extends Controller
{
    public function typeahead()
    {
        $results = Term::typeahead([
            'label', 'description'
        ], [
            'id', 'label', 'description'
        ]);

        return [
            'results' => $results
        ];
    }

    public function index()
	{
	    return [
	        'collection' => Term::filter()
	    ];
	}

	public function create()
    {
        $form = [
            'label' => '',
            'description' => ''
        ];

        return [
            'form' => $form
        ];
    }

    public function store(Request $request)
	{
		abort_in_demo();
	    $request->validate([
	        'label' => 'required|unique:team.terms,label',
	        'description' => 'required'
	    ]);

	    $item = new Term;
	    $item->label = $request->label;
	    $item->description = $request->description;
	    $item->save();

	    return [
	        'saved' => true,
	        'id' => $item->id,
	        'item' => $item
	    ];
	}

	public function show($id)
    {
        $item = Term::findOrFail($id);

        return [
            'model' => $item
        ];
    }

    public function edit($id)
    {
        $item = Term::findOrFail($id);

        return [
            'form' => $item
        ];
    }

    public function update($id, Request $request)
    {
    	abort_in_demo();
        $request->validate([
            'label' => 'required|unique:team.terms,label,'.$id.',id',
	        'description' => 'required'
        ]);

        $item = Term::findOrFail($id);
        $item->fill($request->all());
        $item->save();

        return [
            'saved' => true,
            'id' => $item->id
        ];
    }

    public function destroy($id)
    {
    	abort_in_demo();
        $model = Term::findOrFail($id);

        // if(DB::connection('tenant')->table('terms')->where('category_id', $model->id)->count()) {
        //     return delete_first(__('lang.cannot_delete'));
        // }

        // check settings
        // if(settings()->check('default_item_category_id', $model->id)) {
        //     return delete_first(__('lang.cannot_delete'));
        // }

        $model->delete();

        return [
            'deleted' => true
        ];
    }
}
