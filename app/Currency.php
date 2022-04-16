<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Currency extends Model
{
	use Filterable;

	protected $connection = 'mysql';

    protected $fillable = [
    	'name', 'code', 'precision', 'subunit', 'symbol', 'symbol_first', 'decimal_mark', 'thousands_separator'
    ];
}
