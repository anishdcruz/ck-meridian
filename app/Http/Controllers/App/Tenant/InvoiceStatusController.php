<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\StatusController;
use App\Tenant\Invoice\Status;

class InvoiceStatusController extends StatusController
{
	protected $model = Status::class;

	protected $parent = 'invoices';

	protected $title = 'invoices';

	protected $table = 'invoice_statuses';
}
