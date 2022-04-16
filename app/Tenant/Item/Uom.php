<?php

namespace App\Tenant\Item;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Uom extends Model
{
	use Filterable;

	protected $connection = 'team';

    protected $table = 'item_uoms';

    protected $fillable = ['name'];
}

