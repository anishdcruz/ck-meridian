<?php

namespace App\Tenant\Item;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Category extends Model
{
	use Filterable;

	protected $connection = 'team';

    protected $table = 'item_categories';

    protected $fillable = ['name'];
}
