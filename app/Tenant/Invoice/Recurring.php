<?php

namespace App\Tenant\Invoice;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;
use App\Support\HasManyRelation;
use App\Tenant\Contact\Contact;
use App\Tenant\Term;

class Recurring extends Model
{
	use Filterable;
    use HasManyRelation;

    protected $table = 'recurring_invoices';

    protected $connection = 'team';

    protected $appends = ['next_date'];

    protected $fillable = [
        'send_on', 'send_at', 'frequency',
        'start_date', 'end_date', 'due_after',
        'never_expiry', 'title',
    	'contact_id',
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
    	return $this->hasMany(Line::class, 'invoice_id', 'id');
    }

    public function getNextDateAttribute()
    {
        $date = $startDate = $this->attributes['start_date'];
        $lastDate = $this->attributes['last_generated_date'];

        $frequency = $this->attributes['frequency'];
        $send_on = $this->attributes['send_on'];
        $send_at = $this->attributes['send_at'];

        $days = [
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            7 => 'sunday'
        ];

        $begin = now()->parse($startDate);
        $last = now()->parse($lastDate);
        $now = now();

        if($lastDate) {
            if($frequency == 'weekly') {
                $date = $last->copy()
                    ->modify('next '.$days[$send_on]);
            } else if($frequency == 'monthly') {
                if($begin->lte($now)) {
                    $date = $begin->copy()
                        ->day($send_on);

                } else {

                    $date = $begin->copy()
                        ->addMonth(1)
                        ->day($send_on);
                }
            }
        } else {

            if($frequency == 'weekly') {
                $date = $begin->copy()
                    ->modify('next '.$days[$send_on]);
            } else if($frequency == 'monthly') {
                if($begin->lte($now)) {
                    $date = $begin->copy()
                        ->day($send_on);

                } else {

                    $date = $begin->copy()
                        ->addMonth(1)
                        ->day($send_on);
                }
            }
        }

        return $date->format('Y-m-d');
    }
}
