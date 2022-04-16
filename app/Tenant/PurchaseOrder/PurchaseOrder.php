<?php

namespace App\Tenant\PurchaseOrder;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Support\HasManyRelation;
use App\Tenant\Vendor\Vendor;
use App\Tenant\Term;
use App\Currency;

class PurchaseOrder extends Model
{
    use Filterable;
    use HasManyRelation;

	protected $connection = 'team';

    protected $table = 'purchase_orders';

    protected $fillable = [
    	'vendor_id',
    	'issue_date',
    	'reference',
    	'discount',
    	'tax',
    	'tax_total',
    	'tax_2',
    	'tax_2_total',
    	'term_id',
    	'notes',
    	'foreign_currency_id',
    	'exchange_rate'
    ];

    protected $appends = ['balance_due'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id', 'id');
    }

    public function foreign_currency()
    {
        return $this->belongsTo(Currency::class, 'foreign_currency_id', 'id');
    }

    public function lines()
    {
    	return $this->hasMany(Line::class, 'purchase_order_id', 'id');
    }

    public function status()
    {
    	return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function getBalanceDueAttribute()
    {
        if(isset($this->attributes['grand_total'], $this->attributes['amount_paid'])) {
            return $this->attributes['grand_total'] - $this->attributes['amount_paid'];
        }
        return null;
    }
}
