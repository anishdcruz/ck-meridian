<?php

namespace App\Tenant\PurchaseOrder;

use Illuminate\Database\Eloquent\Model;
use App\Support\Filterable;

class Status extends Model
{
    use Filterable;

	protected $connection = 'team';

    protected $table = 'purchase_order_statuses';

    protected $fillable = ['name', 'color', 'locked'];
}
