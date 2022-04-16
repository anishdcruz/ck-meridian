<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\MeridianWithItemController;
use App\Tenant\Invoice\Invoice;
use App\Tenant\Invoice\Status;
use App\Tenant\Contact\Contact;
use App\Exports\InvoicesExport;
use App\Support\PDF;

class InvoiceController extends MeridianWithItemController
{
	protected $title = 'invoices';

	protected $model = Invoice::class;

	protected $counter = 'invoice';

	protected $statusTable = 'invoice_statuses';

	protected $printFile = 'invoice';

	protected $emailSubject = 'invoice_email_subject';
    protected $emailTemplate = 'invoice_email_template';
    protected $onEmailSent = 'invoice_status_on_sent_id';

	protected $withShow = [
		'status:id,name,color', 'lines.item.uom',
        'contact', 'term', 'quotation:id,number'
	];
	protected $withEdit = [
		'status:id,name,color', 'lines.item.uom',
		'contact', 'term'
	];

    public function getIndex()
    {
        return Invoice::with([
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
            'due_in_days' => 15, // todo settings
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
            'due_in_days' => 'required|integer|min:1',
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

        $form->quotation_id = request('quotation_id', null);

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
        $form->due_date = now()
            ->addDays(request('due_in_days'))
            ->format('Y-m-d');

        $id = team_config('invoice_status_on_create_id');
        $form->status_id = $id;
        $form->payment_code = $this->getNewPaymentCode();
    	return $form;
    }

    public function getNewPaymentCode()
    {
        $name = null;
        do {
            $name = str_random(32);
        } while($this->model::where('payment_code', $name)->first());

        return 'inv_'.$name;
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
    	            $form->quotation_id = null;
    	            $form->issue_date = now()->format('Y-m-d');
    	            $form->due_date = null;
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
    	    'due_in_days' => 'required|integer|min:1',
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
	    $form->due_date = now()
            ->addDays(request('due_in_days'))
            ->format('Y-m-d');
    	return $form;
    }

    public function deliveyNote($id, Request $request, PDF $pdf)
    {
    	$model = $this->model::findOrFail($id);

        return $pdf
            ->preview('app.pdf.delivey_note', [
                'model' => $model
            ]);
    }

    public function confirmDestroy($model, $db)
    {
    	//  check payments
    	$result = confirm_array([
    		check_parent('payments', 'invoice_id', $model)
    	]);

    	if($result) {
    		$model->lines()->delete();
    	}

    	return $result;
    }

    public function setExport(Request $request)
    {
        return new InvoicesExport($request->team());
    }

    public function emailVariables($model)
    {
    	$team = get_team();

    	return [
    		'number' => $model->number,
            'contact_name' => $model->contact->name,
            'due_date' => formatDate($model->due_date),
            'grand_total' => formatMoney($model->grand_total),
            'payment_link' => route('app.invoice.show', ['team' => $team->uuid, 'invoice' => $model->payment_code])
    	];
    }
}
