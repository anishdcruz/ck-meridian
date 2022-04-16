<?php

namespace App\Tenant\Quotation;

use Illuminate\Database\Eloquent\Model;
use App\Tenant\Item\Item;

class Line extends Model
{
	protected $connection = 'team';

    protected $table = 'quotation_lines';

    protected $fillable = ['item_id', 'unit_price', 'qty'];

    public function item()
    {
    	return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function getTotalAttribute()
    {
    	return $this->attributes['qty'] * $this->attributes['unit_price'];
    }
}
