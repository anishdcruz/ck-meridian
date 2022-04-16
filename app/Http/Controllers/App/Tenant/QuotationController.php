<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\MeridianWithItemController;
use App\Tenant\Quotation\Quotation;
use App\Tenant\Quotation\Status;
use App\Tenant\Contact\Contact;
use App\Exports\QuotationsExport;

class QuotationController extends MeridianWithItemController
{
	protected $title = 'quotations';

	protected $model = Quotation::class;

	protected $counter = 'quotation';

	protected $statusTable = 'quotation_statuses';

	protected $printFile = 'quotation';

	protected $withShow = [
		'status:id,name,color', 'lines.item.uom',
        'contact', 'term'
    ];

	protected $withEdit = [
		'status:id,name,color', 'lines.item.uom',
		'contact', 'term'
	];

	protected $emailSubject = 'quotation_email_subject';
    protected $emailTemplate = 'quotation_email_template';
    protected $onEmailSent = 'quotation_status_on_sent_id';

    public function getIndex()
    {
        return Quotation::with([
            'contact:id,name,organization', 'status:id,name,color',
        ])->when(request('contact_id'), function($query) {
            return $query->where('contact_id', request('contact_id'));
        })->when(request('item_id'), function($query) {
            return $query->whereHas('lines', function($q) {
            	$q->where('item_id', request('item_id'));
            });
        })->filter();
    }

    public function createForm()
    {
        $form = [
            'number' => __('app.auto_generated'),
            'contact_id' => null,
            'contact' => null,
            'issue_date' => date('Y-m-d'),
            'expiry_in_days' => 15, // todo settings
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

        if(request()->has('contact_id')) {
            $f = Contact::findOrFail(request('contact_id'));
            $form['contact_id'] = $f->id;
            $form['contact'] = [
                'name' => $f->name,
                'id' => $f->id
            ];
        }

        return $form;
    }

    public function createRules()
    {
    	$rules = [
    	    'contact_id' => 'required|exists:team.contacts,id',
    	    'issue_date' => 'required|date_format:Y-m-d',
    	    'expiry_in_days' => 'required|integer|min:1',
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
        $form->expiry_date = now()
            ->addDays(request('expiry_in_days'))
            ->format('Y-m-d');

        $id = team_config('quotation_status_on_create_id');
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
                    $form->expiry_date = null;
                    break;

                case 'invoice':
                    $form->number = __('app.auto_generated');
                    $form->reference = null;
                    $form->issue_date = now()->format('Y-m-d');
                    $form->due_date = null;
                    $form->due_in_days = 30;
                    $form->quotation_id = $form->id;
                    break;

    	        default:
    	            abort(404, 'Invalid Mode');
    	            break;
    	    }
    	}
    	return $form;
    }

    public function updateRules($id)
    {
    	$rules = [
            'contact_id' => 'required|exists:team.contacts,id',
            'issue_date' => 'required|date_format:Y-m-d',
            'expiry_in_days' => 'required|integer|min:1',
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
	    $form->expiry_date = now()
            ->addDays(request('expiry_in_days'))
            ->format('Y-m-d');
    	return $form;
    }

    public function confirmDestroy($model, $db)
    {
    	//  check invoices
    	$result = confirm_array([
    		check_parent('invoices', 'quotation_id', $model)
    	]);

    	if($result) {
    		$model->lines()->delete();
    	}

    	return $result;
    }

    public function setExport(Request $request)
    {
        return new QuotationsExport($request->team());
    }

    public function emailVariables($model)
    {
    	return [
    		'number' => $model->number,
    		'contact_name' => $model->contact->name
    	];
    }
}
