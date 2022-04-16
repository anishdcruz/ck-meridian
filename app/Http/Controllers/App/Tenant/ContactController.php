<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\MeridianController;
use App\Tenant\Contact\Contact;
use App\Exports\ContactsExport;

class ContactController extends MeridianController
{
	protected $title = 'contacts';

	protected $model = Contact::class;

	protected $counter = 'contact';

	protected $withIndex = ['category'];
	protected $withShow = ['category'];
	protected $withEdit = ['category'];

	protected $typeaheadColumns = [
        'number', 'name'
    ];

	protected $typeaheadRequired = [
        'id', 'number', 'name', 'organization'
    ];

	public function createForm()
	{
		return [
            'name' => '',
            'organization' => '',
            'category' => null,
            'category_id' => '',
            'email' => '',
            'title' => '',
            'department' => '',
            'phone' => '',
            'work' => '',
            'fax' => '',
            'website' => '',
            'primary_address' => '',
            'other_address' => '',
            'tax_id' => null,
            'number' => __('app.auto_generated'),
        ];
	}

	public function createRules()
	{
		$rules = [
		    'name' => 'required|string',
		    'category_id' => 'nullable|integer|exists:team.contact_categories,id',
		    'email' => 'required|email|string|unique:team.contacts,email,',
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

		if_team_config('tax_enable', $rules, 'tax_id', 'nullable|string');

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
		    'category_id' => 'nullable|integer|exists:team.contact_categories,id',
		    'email' => 'required|email|string|unique:team.contacts,email,'.$id.',id',
		    'title' => 'nullable|string',
		    'department' => 'nullable|string',
		    'mobile' => 'nullable|string',
		    'phone' => 'nullable|string',
		    'fax' => 'nullable|string',
		    'website' => 'nullable|string',
		    'primary_address' => 'required|string',
		    'other_address' => 'nullable|string'
		];

		if_team_config('tax_enable', $rules, 'tax_id', 'nullable|string');

		return $rules;
	}

	public function confirmDestroy($model, $db)
	{
		// has many
		// quotation
		// invoices
		// payments
		// refunds
		// recurring-invoices
		return confirm_array([
			check_parent('quotations', 'contact_id', $model),
			check_parent('invoices', 'contact_id', $model),
			check_parent('recurring_invoices', 'contact_id', $model),
			check_parent('payments', 'contact_id', $model),
			check_parent('refunds', 'contact_id', $model)
		]);
	}

	public function setExport($request)
	{
		return new ContactsExport($request->team());
	}
}
