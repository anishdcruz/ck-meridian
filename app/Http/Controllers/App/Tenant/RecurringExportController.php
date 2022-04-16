<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tenant\RecurringExport;
use App\TeamRecurringExport;

class RecurringExportController extends Controller
{
	protected function allowedResources()
	{
		return implode(',', [
			'contacts',
			'items',
            'quotations',
            'invoices',
            'payments',
            'refunds',
            'recurring-invoices',
            'vendors',
            'purchase-orders',
            'payments-made',
            'expenses'
		]);
	}

	public function index()
    {
        $collection = RecurringExport::where(function($query) {
            $query->where('user_id', auth()->id());
        })->filter();

        return [
            'collection' => $collection
        ];
    }

    public function store(Request $request)
    {
    	abort_in_demo();
    	$request->validate([
    		'name' => 'required|string',
    		'params' => 'required|array',
    		'resource' => 'required|in:'.$this->allowedResources(),
    		'frequency' => 'required|in:daily,weekly,monthly',
            'send_on' => 'required_unless:frequency,daily|integer|max:31',
            'send_at' => 'required|string',
            'email_to' => 'required|email'
    	]);

    	$form = new RecurringExport($request->all());

    	$form->params = json_encode($request->params);

    	$r = new TeamRecurringExport;

    	$r->fill(request()->only([
    	    'frequency'
    	]));

    	$team = get_team();

    	$sendAt = request('send_at');


    	$timestamp = $sendAt.':00';

    	$date = \Carbon\Carbon::createFromFormat('H:i:s', $timestamp, $team->timezone);
    	$date->setTimezone('UTC');

    	$r->send_on = request('send_on');
    	$r->send_at = $date->format('H:i');
    	$r->team_id = $team->id;

    	$form->user_id = auth()->id();
    	$form->save();

    	$r->export_id = $form->id;
        $r->save();

    	return [
    		'saved' => true,
    		'id' => $form->id
    	];
    }

    public function show($id)
    {
        $pp = RecurringExport::findOrFail($id);

        return [
            'model' => $pp
        ];
    }

    public function destroy($id)
    {
    	abort_in_demo();
        $model = RecurringExport::findOrFail($id);

        DB::table('team_recurring_exports')
        	->where([
        		'team_id' => get_team()->id,
        		'export_id' => $model->id
        	])->delete();

        $model->delete();

        return [
            'deleted' => true
        ];
    }
}
