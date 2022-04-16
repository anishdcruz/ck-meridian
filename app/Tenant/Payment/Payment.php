<?php

namespace App\Tenant\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Tenant\Contact\Contact;
use App\Tenant\Invoice\Invoice;

class Payment extends Model
{
	use Filterable;

    protected $table = 'payments';

    protected $connection = 'team';

    protected $appends = ['available_amount'];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function invoice()
    {
    	return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function getAvailableAmountAttribute()
    {
    	$ar = isset($this->attributes['amount_received']) ? $this->attributes['amount_received'] : 0;
    	$are = isset($this->attributes['amount_refunded']) ? $this->attributes['amount_refunded'] : 0;
        return $ar - $are;
    }
}
