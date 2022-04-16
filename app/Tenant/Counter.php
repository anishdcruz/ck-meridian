<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
	protected $connection = 'team';

    protected $appends = ['number'];

	public function getNumberAttribute()
	{
	    return $this->attributes['prefix'].$this->attributes['value'];
	}
}
