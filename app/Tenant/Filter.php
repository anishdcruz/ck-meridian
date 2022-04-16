<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use App\Tenant\Contact\Contact;
use Illuminate\Support\Arr;
use App\Tenant\Item\Item;
use App\Tenant\Quotation\Quotation;
use App\Tenant\Invoice\Invoice;
use App\Tenant\Payment\Payment;
use App\Tenant\Refund\Refund;
use App\Tenant\Invoice\Recurring;
use App\Tenant\Vendor\Vendor;
use App\Tenant\PurchaseOrder\PurchaseOrder;
use App\Tenant\PaymentMade\PaymentMade;
use App\Tenant\Expense\Expense;
use App\RecurringExport;

class Filter extends Model
{
	protected $table = 'filters';

	protected $connection = 'team';

	protected $fillable = [
		'name', 'shared_with', 'params', 'user_id', 'resource', 'filter_match'
	];

	public function getParamsAttribute()
	{
		return json_decode($this->attributes['params'], 1);
	}

	public function model()
	{
		$model = $this->findModal($this->attributes['resource']);
		return $model::getModel();
	}

	public function findModal($resource)
	{
		$models = [
			'contacts' => Contact::class,
			'items' => Item::class,
            'quotations' => Quotation::class,
            'invoices' => Invoice::class,
            'payments' => Payment::class,
            'refunds' => Refund::class,
            'recurring-invoices' => Recurring::class,
            'vendors' => Vendor::class,
            'purchase-orders' => PurchaseOrder::class,
            'payments-made' => PaymentMade::class,
            'expenses' => Expense::class,
            'recurring-exports' => RecurringExport::class
		];

		return $models[$resource];
	}

	public function getParams()
	{
		$f = [];

		foreach ($this->getParamsAttribute() as $filter) {
			$temp = [
				'column' => array_get($filter, 'column.name'),
				'operator' => array_get($filter, 'operator.name')
			];

			if(isset($filter['query_1']) && is_array($filter['query_1'])) {
				$list = array_map(function($i) {
					return $i['value'];
				}, $filter['query_1']);

				$temp['query_1'] = implode(',,', $list);
			} else {
				$temp['query_1'] = $filter['query_1'];
			}
			$temp['query_2'] = $filter['query_2'];
			array_push($f, $temp);
		}

		return $f;
	}
}
