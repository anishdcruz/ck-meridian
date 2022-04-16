<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\MeridianWithItemController;
use App\Tenant\PurchaseOrder\PurchaseOrder;
use App\Tenant\PurchaseOrder\Status;
use App\Tenant\Vendor\Vendor;
use App\Exports\PurchaseOrdersExport;

class PurchaseOrderController extends MeridianWithItemController
{
	protected $title = 'purchase_orders';

	protected $model = PurchaseOrder::class;

	protected $counter = 'purchase_order';

	protected $statusTable = 'purchase_order_statuses';

	protected $printFile = 'purchase_order';

	protected $withShow = [
		'status:id,name,color', 'lines.item.uom',
        'vendor', 'term', 'foreign_currency'
    ];

	protected $withEdit = [
		'status:id,name,color', 'lines.item.uom',
		'vendor', 'term', 'foreign_currency'
	];

	protected $emailSubject = 'purchase_order_email_subject';
    protected $emailTemplate = 'purchase_order_email_template';
    protected $onEmailSent = 'purchase_order_status_on_sent_id';

    public function getIndex()
    {
        return PurchaseOrder::with([
            'vendor:id,name,organization', 'status:id,name,color',
        ])->when(request('vendor_id'), function($query) {
            return $query->where('vendor_id', request('vendor_id'));
        })->when(request('item_id'), function($query) {
            return $query->whereHas('lines', function($q) {
            	$q->where('item_id', request('item_id'));
            });
        })->filter();
    }

    public function createForm()
    {
        $form = [
        	'foreign_currency' => null,
        	'foreign_currency_id' => null,
        	'exchange_rate' => null,
            'number' => __('app.auto_generated'),
            'vendor_id' => null,
            'vendor' => null,
            'issue_date' => date('Y-m-d'),
            'reference' => null,
            'discount' => 0,
	    	'tax' => 0,
	    	'tax_total'  => 0,
	    	'tax_2' => 0,
	    	'tax_2_total' => 0,
            'term' => null,
	    	'term_id' => null,
	    	'notes' => null,
	    	'lines' => [
                [
                    'item' =>  null,
                    'item_id' => null,
                    'qty' => 1,
                    'unit_price' => 0
                ]
            ]
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
    	    'issue_date' => 'required|date_format:Y-m-d',
    	    'reference' => 'nullable',
    	    'term_id' => 'required|exists:team.terms,id',
    	    'lines' => 'required|array|min:1',
    	    'lines.*.item_id' => 'required|integer|exists:team.items,id',
    	    'lines.*.unit_price' => 'required|numeric|min:0',
    	    'lines.*.qty' => 'required|numeric|min:0',
    	];

    	if_team_config('enable_discount', $rules, 'discount', 'required|numeric|min:0');
    	if_team_config('tax_enable', $rules, 'tax', 'required|numeric|min:0');
    	if_team_config('tax_2_enable', $rules, 'tax_2', 'required|numeric|min:0');

    	return $rules;
    }

    public function inCreateTransaction($form)
    {
    	$items = collect(request('lines'));

    	$form->sub_total = $items->sum(function($item) {
            return $item['qty'] * $item['unit_price'];
        });

        $subTotalAfterDiscount = 0;

        if(team_config('enable_discount')) {
            $form->discount = request('discount');
            $subTotalAfterDiscount = $form->sub_total - $form->discount;
        } else {
            $subTotalAfterDiscount = $form->sub_total;
        }

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

        $id = team_config('purchase_order_status_on_create_id');
        $form->status_id = $id;

    	return $form;
    }

    public function inShow($model)
    {
    	$model->all_statuses = Status::where(function($query) use ($model) {
    			$query->where('id', '<>', $model->status_id)
    				->where('locked', 0);
    		})
    		->get();

    	return $model;
    }

    public function inEdit($form)
    {
    	if(request()->has('mode')) {
    	    switch (request('mode')) {
    	        case 'clone':
    	            $form->number = __('app.auto_generated');
    	            $form->reference = null;
    	            $form->issue_date = now()->format('Y-m-d');
    	            break;

    	        default:
    	            abort(404, 'Invalid Mode');
    	            break;
    	    }
    	}
    	return $form;
    }

    public  function updateRules($id)
    {
    	$rules = [
            'vendor_id' => 'required|exists:team.vendors,id',
            'issue_date' => 'required|date_format:Y-m-d',
            'reference' => 'nullable',
            'term_id' => 'required|exists:team.terms,id',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|integer|exists:team.items,id',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.qty' => 'required|numeric|min:0',
        ];

        if_team_config('enable_discount', $rules, 'discount', 'required|numeric|min:0');
    	if_team_config('tax_enable', $rules, 'tax', 'required|numeric|min:0');
    	if_team_config('tax_2_enable', $rules, 'tax_2', 'required|numeric|min:0');

    	return $rules;
    }

    public function inUpdateTransaction($form)
    {
		$items = collect(request('lines'));

		$form->sub_total = $items->sum(function($item) {
	        return $item['qty'] * $item['unit_price'];
	    });

	    $subTotalAfterDiscount = 0;

	    if(team_config('enable_discount')) {
	        $form->discount = request('discount');
	        $subTotalAfterDiscount = $form->sub_total - $form->discount;
	    } else {
	        $subTotalAfterDiscount = $form->sub_total;
	    }

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

    public function confirmDestroy($model, $db)
    {
    	//  check payments
    	$result = confirm_array([
    		check_parent('payments_made', 'purchase_order_id', $model)
    	]);

    	if($result) {
    		$model->lines()->delete();
    	}

    	return $result;
    }

    public function setExport($request)
    {
        return new PurchaseOrdersExport($request->team());
    }

    public function getEmailForm($model)
    {
    	return [
    	    'email_to' => $model->vendor->email
    	];
    }

    public function emailVariables($model)
    {
    	return [
    		'number' => $model->number,
            'contact_name' => $model->vendor->name
    	];
    }
}
