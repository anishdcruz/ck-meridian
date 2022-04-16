<?php

namespace App\Tenant\PaymentMade;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Tenant\Vendor\Vendor;
use App\Tenant\PurchaseOrder\PurchaseOrder;

class PaymentMade extends Model
{
	use Filterable;

    protected $table = 'payments_made';

    protected $connection = 'team';

    protected $fillable = [
    	'payment_date', 'method', 'amount_paid', 'transaction_fees',
    	'note'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function purchaseOrder()
    {
    	return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }
}
