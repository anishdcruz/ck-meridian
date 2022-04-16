<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\MeridianController;
use App\Tenant\Item\Item;
use App\Exports\ItemsExport;

class ItemController extends MeridianController
{
	protected $title = 'items';

	protected $model = Item::class;
	protected $counter = 'item';

	protected $withIndex = ['category:id,name', 'uom:id,name'];
	protected $withShow = ['category:id,name', 'uom:id,name'];
	protected $withEdit = ['category:id,name', 'uom:id,name'];

	protected $autoNumberField = 'code';

    public function typeahead()
    {
        $columns = ['code', 'description'];
        $results = Item::with(['uom:id,name'])
            ->when(request('query'), function($query) use ($columns) {
                foreach($columns as $column) {
                    $query->orWhere($column, 'like', '%'.request('query').'%');
                }
            })
            ->limit(10)
            ->get();

        return [
            'results' => $results
        ];
    }

    public function createForm()
    {
        return [
            'category_id' => '',
            'category' => null,
            'uom_id' => '',
            'uom' => null,
            'unit_price' => 0,
            'description' => '',
            'code' => __('app.auto_generated')
        ];
    }

    public function createRules()
    {
    	return [
            'category_id' => 'required|integer|exists:team.item_categories,id',
            'uom_id' => 'required|integer|exists:team.item_uoms,id',
            'unit_price' => 'required|numeric|min:0',
            'description' => 'required|string',
        ];
    }

    public function storeResponse($form)
    {
    	return [
    		'saved' => true,
    		'id' => $form->id,
    		'item' => $form->load('uom')
    	];
    }

    public function updateRules($id)
    {
    	return [
            'category_id' => 'required|integer|exists:team.item_categories,id',
            'uom_id' => 'required|integer|exists:team.item_uoms,id',
            'unit_price' => 'required|numeric|min:0',
            'description' => 'required|string'
        ];
    }

    public function confirmDestroy($model, $db)
	{
		// check quotation, invoices, recurring-invoices, purchase orders
		return confirm_array([
			check_parent('quotation_lines', 'item_id', $model),
			check_parent('invoice_lines', 'item_id', $model),
			check_parent('purchase_order_lines', 'item_id', $model)
		]);
	}

    public function setupExport(Request $request)
    {
        return new ItemsExport($request->team());
    }
}
