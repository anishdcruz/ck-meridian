<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\MeridianWithItemController;
use App\Tenant\Invoice\Recurring as Invoice;
use App\Tenant\Invoice\Status;
use App\Tenant\Contact\Contact;
use App\TeamRecurringInvoice;
use App\Exports\RecurringInvoicesExport;
use DB;

class RecurringInvoiceController extends MeridianWithItemController
{
	protected $title = 'recurring_invoices';
	protected $hasCounter = false;
	protected $model = Invoice::class;

	protected $counter = 'recurring_invoice';

	protected $printFile = 'recurring_invoice';

	protected $withShow = [
		'lines.item.uom', 'contact', 'term'
    ];

	protected $withEdit = [
		'lines.item.uom', 'contact', 'term'
	];
    public function getIndex()
    {
        return Invoice::with([
            'contact:id,name,organization'
        ])->when(request('contact_id'), function($query) {
            return $query->where('contact_id', request('contact_id'));
        })->filter();
    }

    public function createForm()
    {
    	 $form = [
            'title' => '',
            'start_date' => date('Y-m-d'),
            'end_date' => null,
            'frequency' => 'monthly',
            'send_on' => '1',
            'send_at' => '9:00',
            'never_expiry' => 1,
            'due_after' => 30,
            'contact_id' => null,
            'contact' => null,
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
            'frequency' => 'required|in:daily,weekly,monthly',
            'send_on' => 'required_unless:frequency,daily|integer|max:31',
            'due_after' => 'required|integer|min:1',
            'send_at' => 'required|string',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'sometimes|nullable|date_format:Y-m-d|after_or_equal:start_date',
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

	public function afterCreate($form)
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

        $r = new TeamRecurringInvoice;

        $r->fill(request()->only([
            'frequency', 'never_expiry'
        ]));

        $team = get_team();

        $start = request('start_date');
        $sendAt = request('send_at');


        $timestamp = $start.' '.$sendAt.':00';
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, $team->timezone);
        $date->setTimezone('UTC');
        $send_on = request('frequency') == 'weekly' ?  $date->dayOfWeek: $date->format('d');

        if($end = request('end_date')) {
        	$timestamp2 = $end.' '.$sendAt.':00';
        	$e = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestamp2, $team->timezone);
        	$e->setTimezone('UTC');
        	$r->end_date = $e;
        }

        $r->start_date = $date;
        $r->send_on = $send_on;
        $r->send_at = $date->format('H:i');
        $r->team_id = $team->id;

        $form->storeHasMany([
            'lines' => request('lines')
        ]);

        $r->recurring_id = $form->id;
        $r->save();
    	return $form;
    }

    public function updateRules($id)
    {
    	$rules = [
            'contact_id' => 'required|exists:team.contacts,id',
            'frequency' => 'required|in:daily,weekly,monthly',
            'due_after' => 'required|integer|min:1',
            'send_at' => 'required|string',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'sometimes|nullable|date_format:Y-m-d|after_or_equal:start_date',
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
	    $team = get_team();

	    $start = request('start_date');
        $sendAt = request('send_at');


        $timestamp = $start.' '.$sendAt.':00';
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, $team->timezone);
        $form->send_on = request('frequency') == 'weekly' ?  $date->dayOfWeek: $date->format('d');

        if($end = request('end_date')) {
        	$timestamp2 = $end.' '.$sendAt.':00';
        	$e = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestamp2, $team->timezone);
        	$e->setTimezone('UTC');
        	$form->end_date = $e;
        }

	    $r = TeamRecurringInvoice::where('team_id', $team->id)
            ->where('recurring_id', $form->id)
            ->first();

        $r->fill(request()->only([
            'frequency', 'never_expiry'
        ]));



        $start = request('start_date');
        $sendAt = request('send_at');


        $timestamp = $start.' '.$sendAt.':00';
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, $team->timezone);
        $date->setTimezone('UTC');
        $send_on = request('frequency') == 'weekly' ?  $date->dayOfWeek: $date->format('d');

        if($end = request('end_date')) {
        	$timestamp2 = $end.' '.$sendAt.':00';
        	$e = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestamp2, $team->timezone);
        	$e->setTimezone('UTC');
        	$r->end_date = $e;
        }

        $r->start_date = $date;
        $r->send_on = $send_on;
        $r->send_at = $date->format('H:i');
        $r->save();
    	return $form;
    }

    public function confirmDestroy($model, $db)
    {
    	DB::table('team_recurring_invoices')
    		->where([
    			'team_id' => get_team()->id,
    			'recurring_id' => $model->id
    		])->delete();

    	$model->lines()->delete();

    	return true;
    }

    public function setExport(Request $request)
    {
        return new RecurringInvoicesExport($request->team());
    }
}
