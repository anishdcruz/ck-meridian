<?php

namespace App\Tenant\Quotation;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Support\HasManyRelation;
use App\Tenant\Contact\Contact;
use App\Tenant\Term;

class Quotation extends Model
{
    use Filterable;
    use HasManyRelation;

	protected $connection = 'team';

    protected $table = 'quotations';

    protected $fillable = [
    	'contact_id',
    	'issue_date',
    	'expiry_date',
    	'expiry_in_days',
    	'reference',
    	'discount',
    	'tax',
    	'tax_total',
    	'tax_2',
    	'tax_2_total',
    	'term_id',
    	'notes'
    ];

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
    	return $this->hasMany(Line::class, 'quotation_id', 'id');
    }

    public function status()
    {
    	return $this->belongsTo(Status::class, 'status_id', 'id');
    }
}
