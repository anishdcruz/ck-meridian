<?php

namespace App\Tenant\Invoice;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Status extends Model
{
    use Filterable;

	protected $connection = 'team';

    protected $table = 'invoice_statuses';

    protected $fillable = ['name', 'color', 'locked'];
}
