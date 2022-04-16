<?php

namespace App\Http\Controllers\App\Tenant;

use App\Http\Controllers\MeridianController;
use App\Tenant\Vendor\Vendor;
use App\Exports\VendorsExport;

class VendorController extends MeridianController
{
	protected $title = 'vendors';

	protected $model = Vendor::class;

	protected $counter = 'vendor';

	protected $withIndex = ['category', 'currency:id,name,code'];
	protected $withShow = ['category', 'currency:id,name,code'];
	protected $withEdit = ['category', 'currency:id,name,code'];

	protected $typeaheadColumns = [
        'number', 'name', 'organization'
    ];

	protected $typeaheadRequired = [
        'id', 'number', 'name', 'organization'
    ];

    public function typeahead()
    {
        $collection = $this->model::with(['currency'])
        	->when(request('query'), function($query) {
                foreach($this->typeaheadColumns as $column) {
                    $query->orWhere($column, 'like', '%'.request('query').'%');
                }
            })
            ->limit(10)
            ->get();

        return [
            'results' => $collection
        ];
    }

    public function createForm()
    {
        $form = [
            'name' => '',
            'organization' => '',
            'currency' => null,
            'currency_id' => null,
            'category' => null,
            'category_id' => null,
            'email' => '',
            'title' => '',
            'department' => '',
            'mobile' => '',
            'work' => '',
            'fax' => '',
            'website' => '',
            'primary_address' => '',
            'other_address' => '',
            'number' => __('app.auto_generated'),
        ];

        return $form;
    }

    public function createRules()
    {
        $rules = [
            'name' => 'required|string',
            'category_id' => 'nullable|integer|exists:team.vendor_categories,id',
            'email' => 'required|email|string|unique:team.vendors,email,',
            'title' => 'nullable|string',
            'organization' => 'nullable|string',
            'department' => 'nullable|string',
            'mobile' => 'nullable|string',
            'phone' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'primary_address' => 'required|string',
            'other_address' => 'nullable|string',
        ];

        return $rules;
    }

    public function storeResponse($form)
    {
    	return [
    		'saved' => true,
    		'id' => $form->id,
    		'item' => $form
    	];
    }

    public function updateRules($id)
    {
        $rules = [
            'organization' => 'nullable|string',
            'name' => 'required|string',
            'category_id' => 'nullable|integer|exists:team.vendor_categories,id',
            'email' => 'required|email|string|unique:team.vendors,email,'.$id.',id',
            'title' => 'nullable|string',
            'department' => 'nullable|string',
            'mobile' => 'nullable|string',
            'phone' => 'nullable|string',
            'fax' => 'nullable|string',
            'website' => 'nullable|string',
            'primary_address' => 'required|string',
            'other_address' => 'nullable|string'
        ];

        return $rules;
    }

	public function confirmDestroy($model, $db)
	{
		//  check payments, purchase order, expenses
		return confirm_array([
			check_parent('payments_made', 'vendor_id', $model),
			check_parent('expenses', 'vendor_id', $model),
			check_parent('purchase_orders', 'vendor_id', $model)
		]);
	}

    public function setExport($request)
    {
        return new VendorsExport($request->team());
    }
}
