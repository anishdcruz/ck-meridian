<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\MeridianController;
use App\Tenant\Expense\Expense;
use App\Exports\ExpensesExport;
use App\Tenant\Vendor\Vendor;

class ExpenseController extends MeridianController
{
	protected $title = 'expenses';

	protected $model = Expense::class;

	protected $counter = 'expense';

	protected $withShow = ['vendor', 'category'];
	protected $withEdit = ['category'];

	protected $typeaheadColumns = [
        'number', 'name'
    ];

	protected $typeaheadRequired = [
        'id', 'number', 'name', 'organization'
    ];

    public function getIndex()
    {
    	return $this->model::with([
	        'vendor:id,name,organization', 'category:id,name',
	    ])->when(request('vendor_id'), function($query) {
	        return $query->where('vendor_id', request('vendor_id'));
	    })->filter();
    }

	 public function createForm()
    {
        $form = [
            'number' => __('app.auto_generated'),
            'vendor_id' => null,
            'vendor' => null,
            'category_id' => null,
            'category' => null,
            'payment_date' => date('Y-m-d'),
            'method' => 'cheque',
            'sub_total' => 0,
            'reference' => null,
	    	'tax' => 0,
	    	'tax_total'  => 0,
	    	'tax_2' => 0,
	    	'tax_2_total' => 0,
	    	'note' => null,
        ];

        if(request()->has('vendor_id')) {
            $f = Vendor::findOrFail(request('vendor_id'));
            $form['vendor_id'] = $f->id;
            $form['vendor'] = [
                'name' => $f->name,
                'id' => $f->id
            ];
        }

        return $form;
    }

    public function createRules()
    {
        $rules = [
            'vendor_id' => 'required|exists:team.vendors,id',
            'payment_date' => 'required|date_format:Y-m-d',
            'reference' => 'nullable',
            'method' => 'required',
            'category_id' => 'required|exists:team.expense_categories,id',
            'sub_total' => 'required|numeric|min:0',
            'note' => 'required'
        ];

        if_team_config('tax_enable', $rules, 'tax', 'required|numeric|min:0');
        if_team_config('tax_2_enable', $rules, 'tax_2', 'required|numeric|min:0');

        return $rules;
    }

    public function inCreateTransaction($form)
    {
    	$subTotalAfterDiscount = $form->sub_total;
    	$total = 0;

    	if(team_config('tax_enable')) {
    		$form->tax = request('tax');
            $form->tax_total = $subTotalAfterDiscount * ($form->tax / 100);

            if(team_config('tax_2_enable')) {
                $form->tax_2 = request('tax_2');
                $form->tax_2_total = $subTotalAfterDiscount * ($form->tax_2 / 100);
                $total = $subTotalAfterDiscount + $form->tax_total + $form->tax_2_total;
            } else {
                $total = $subTotalAfterDiscount + $form->tax_total;
            }
    	} else {
            $total = $subTotalAfterDiscount;
        }

        $form->grand_total = $total;

        return $form;
    }

    public function afterCreate($form)
    {
    	$form->vendor->addPayment($form->grand_total);
    	return $form;
    }

    public function confirmDestroy($model, $db)
    {
    	$model->vendor->removePayment($model->grand_total);
    	return true;
    }


    public function setExport(Request $request)
    {
        return new ExpensesExport($request->team());
    }
}
