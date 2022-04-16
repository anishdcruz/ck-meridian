<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\StatusController;
use App\Tenant\PurchaseOrder\Status;

class PurchaseOrderStatusController extends StatusController
{
	protected $model = Status::class;

	protected $parent = 'purchase_orders';

	protected $title = 'purchase_orders';

	protected $table = 'purchase_order_statuses';
}
