<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\MeridianController;
use App\Tenant\PaymentMade\PaymentMade;
use App\Tenant\PurchaseOrder\PurchaseOrder;
use App\Exports\PaymentsMadeExport;

class PaymentMadeController extends MeridianController
{
	protected $title = 'payments_made';

	protected $model = PaymentMade::class;

	protected $counter = 'payment_made';

	protected $withShow = ['vendor', 'purchaseOrder'];

    public function getIndex()
    {
        return PaymentMade::with([
            'vendor:id,name,organization', 'purchaseOrder:id,number',
        ])->when(request('vendor_id'), function($query) {
            return $query->where('vendor_id', request('vendor_id'));
        })->when(request('purchase_order_id'), function($query) {
            return $query->where('purchase_order_id', request('purchase_order_id'));
        })->filter();
    }

    public function createRules()
    {
    	return [
    		'purchase_order_id' => 'required|integer',
    		'payment_date' => 'required|date_format:Y-m-d',
    		'method' => 'required',
    		'amount_paid' => 'required|numeric|min:1',
    		'transaction_fees' => 'required|numeric|min:0',
    		'note' => 'required'
    	];
    }

    public function inCreateTransaction($form)
    {
    	$purchase_order = PurchaseOrder::findOrFail(request('purchase_order_id'));
    	$form->vendor_id = $purchase_order->vendor_id;
        $form->purchase_order_id = $purchase_order->id;
        $form->net_amount = request('amount_paid') + request('transaction_fees');

        return $form;
    }

    public function afterCreate($form)
    {
    	$config = team_configs([
            'purchase_order_status_on_partially_paid_id',
            'purchase_order_status_on_paid_id',
        ]);

        $purchase_order = $form->purchaseOrder;

        $amount = 0;
        $amountPaid = 0;

        if($purchase_order->foreign_currency_id) {
        	// convert local to foreign
        	$amountPaid = $form->amount_paid * $purchase_order->exchange_rate * 100;
        	$amount = $purchase_order->amount_paid + $amountPaid;
        } else {
        	$amount = $purchase_order->amount_paid + $form->amount_paid;
        }


        $purchase_order->amount_paid = $amount;
        $purchase_order->status_id = $config['purchase_order_status_on_partially_paid_id'];
        if($purchase_order->amount_paid >= $purchase_order->grand_total) {
            $purchase_order->status_id = $config['purchase_order_status_on_paid_id'];
            $purchase_order->paid_at = $form->created_at;
        }
        $purchase_order->save();

        $vendor = $form->vendor;
        $vendor->total_paid += $form->amount_paid;
        $vendor->save();

        return $form;
    }

    public function confirmDestroy($model, $db)
    {
    	// undo changes to purchase order
		$config = team_configs([
	        'purchase_order_status_on_partially_paid_id'
	    ]);

	    $purchase_order = $model->purchaseOrder;
	    $amount = $purchase_order->amount_paid - $model->amount_paid;
	    $purchase_order->amount_paid = $amount;
	    $purchase_order->status_id = $config['purchase_order_status_on_partially_paid_id'];
	    $purchase_order->save();

    	$vendor = $model->vendor;
    	$vendor->total_paid -= $model->amount_paid;
    	$vendor->save();

    	return true;
    }

    public function setExport(Request $request)
    {
        return new PaymentsMadeExport($request->team());
    }
}
