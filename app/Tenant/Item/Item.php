<?php

namespace App\Tenant\Item;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Item extends Model
{
    use Filterable;

	protected $connection = 'team';

	protected $table = 'items';

	protected $fillable = [
		'description', 'category_id', 'uom_id',
        'unit_price'
	];

    protected $appends = ['full_code', 'price'];

    public function category()
    {
    	return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function uom()
    {
    	return $this->belongsTo(Uom::class, 'uom_id', 'id');
    }

    public function getFullCodeAttribute()
    {
        if(isset($this->attributes['description'])) {
            return $this->attributes['code'] . ' - ' . $this->attributes['description'];
        }
        return $this->attributes['code'];
    }

    public function getPriceAttribute()
    {
        if(isset($this->attributes['unit_price'])) {
            return formatMoney($this->attributes['unit_price']);
        }
        return 0;
    }
}
