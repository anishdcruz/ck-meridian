<?php

namespace App\Organization;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';

    public function category()
    {
    	return $this->belongsTo(Category::class, 'organization_category_id', 'id');
    }
}
