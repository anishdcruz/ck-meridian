<?php

namespace App\Tenant\Refund;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Tenant\Contact\Contact;
use App\Tenant\Payment\Payment;

class Refund extends Model
{
	use Filterable;

    protected $table = 'refunds';

    protected $connection = 'team';

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function payment()
    {
    	return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

}
