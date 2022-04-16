<?php

namespace App\Tenant\Quotation;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Status extends Model
{
    use Filterable;

	protected $connection = 'team';

    protected $table = 'quotation_statuses';

    protected $fillable = ['name', 'color', 'locked'];
}
