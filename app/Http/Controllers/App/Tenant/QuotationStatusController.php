<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\StatusController;
use App\Tenant\Quotation\Status;

class QuotationStatusController extends StatusController
{
	protected $model = Status::class;

	protected $parent = 'quotations';

	protected $title = 'quotations';

	protected $table = 'quotation_statuses';
}
