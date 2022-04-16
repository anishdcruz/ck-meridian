<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tenant\Refund\Refund;
use Mail;
use App\Exports\RefundsExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class RefundController extends Controller
{
	public function search()
	{
		$this->authorize('module', 'refunds.view');
        $collection = Refund::search();

		return [
    		'collection' => $collection
    	];
	}

    public function index()
    {
    	$this->authorize('module', 'refunds.view');
        $collection = Refund::with([
            'contact:id,name,organization', 'payment',
        ])->when(request('contact_id'), function($query) {
            return $query->where('contact_id', request('contact_id'));
        })->when(request('payment_id'), function($query) {
            return $query->where('payment_id', request('payment_id'));
        })->filter();

        return [
            'collection' => $collection
        ];
    }

    public function show($id)
    {
    	$this->authorize('module', 'refunds.view');
        $pp = Refund::with([
            'contact', 'payment'
            ])
            ->findOrFail($id);


        return [
            'model' => $pp
        ];
    }

    public function confirmDestroy($model, $db)
    {
        $payment = $model->payment;
        $payment->amount_refunded -= $model->amount;
        $payment->save();

    	$contact = $model->contact;
    	$contact->total_revenue -= $model->amount;
    	$contact->save();

    	return true;
    }

    public function destroy($id)
    {
    	abort_in_demo();
    	$this->authorize('module', 'refunds.delete');
    	$found = Refund::findOrFail($id);

    	if(!$this->confirmDestroy($found, new DB)) {
	    	return response()->json([
	        	'message' => __('app.cannot_delete'),
		        'errors' => []
		    ], 422);
    	}

    	$found->delete();

    	return [
    		'deleted' => true,
    		'id' => $id
    	];
    }

    public function export(Request $request)
    {
    	abort_in_demo();
    	$this->authorize('module', 'refunds.export');
        return Excel::download(new RefundsExport($request->team()), 'refunds.xlsx');
    }
}
