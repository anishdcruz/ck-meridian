<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Plan extends Model
{
	use Filterable;

	protected $fillable = [
		'name',
		'slug',
		'gateway_id',
		'time_period',
		'notes',
		'price',
		'active',
		'list',
		'limits'
	];

	public function setNameAttribute($value)
	{
		$this->attributes['slug'] = str_slug($value);
		$this->attributes['name'] = $value;
	}

	public function setLimitsAttribute($value)
	{
		$this->attributes['limits'] = json_encode($value);
	}

	public function setListAttribute($value)
	{
		$this->attributes['list'] = json_encode($value);
	}

    public function getListAttribute()
    {
    	return json_decode($this->attributes['list'], true);
    }

    public function getLimitsAttribute()
    {
    	return json_decode($this->attributes['limits'], true);
    }

    public function getFindLimitsAttribute()
    {
    	$collection = collect(json_decode($this->attributes['limits'], true));

    	$keyed = $collection->mapWithKeys(function ($item) {
    	    return [$item['name'] => $item['max']];
    	});

    	return $keyed->toArray();
    }
}
