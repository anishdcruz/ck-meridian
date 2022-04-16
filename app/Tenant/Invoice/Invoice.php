<?php

namespace App\Tenant\Invoice;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Support\HasManyRelation;
use App\Tenant\Contact\Contact;
use App\Tenant\Term;
use App\Tenant\Quotation\Quotation;

class Invoice extends Model
{
    use Filterable;
    use HasManyRelation;

	protected $connection = 'team';

    protected $table = 'invoices';

    protected $fillable = [
    	'contact_id',
    	'issue_date',
    	'due_date',
    	'due_in_days',
    	'reference',
    	'discount',
    	'tax',
    	'tax_total',
    	'tax_2',
    	'tax_2_total',
    	'term_id',
    	'notes'
    ];

    protected $nonFilterable = [
        'payment_code'
    ];

    protected $nonSearchable = [
        'payment_code'
    ];

    protected $appends = ['balance_due'];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id', 'id');
    }

    public function lines()
    {
    	return $this->hasMany(Line::class, 'invoice_id', 'id');
    }

    public function status()
    {
    	return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function quotation()
    {
    	return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    public function getBalanceDueAttribute()
    {
        if(isset($this->attributes['grand_total'], $this->attributes['amount_paid'])) {
            return $this->attributes['grand_total'] - $this->attributes['amount_paid'];
        }
        return null;
    }
}
