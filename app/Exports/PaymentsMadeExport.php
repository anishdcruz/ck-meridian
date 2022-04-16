<?php

namespace App\Exports;

use App\Tenant\PaymentMade\PaymentMade;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PaymentsMadeExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
	protected $team;

	protected $filter;

	public function __construct($team, $filter = null)
	{
		$this->team = $team;
		$this->filter = $filter;
	}

    public function headings(): array
    {
        return [
            __('app.number'),
            __('app.payment_date'),
            __('app.name'),
            __('app.organization'),
            __('app.purchase_order'),
            __('app.method'),
            __('app.note'),
            __('app.amount_paid'),
            __('app.transaction_fees'),
            __('app.net_amount'),
            __('app.created_at')
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->number,
            formatDate($payment->payment_date),
            $payment->vendor->name,
            $payment->vendor->organization,
            $payment->purchaseOrder->number,
            $payment->method,
            $payment->note,
            formatMoney($payment->amount_paid, false),
            formatMoney($payment->transaction_fees, false),
            formatMoney($payment->net_amount, false),
            formatDate($payment->created_at),
        ];
    }

    public function collection()
    {
    	if($this->filter) {
    		return PaymentMade::serverExport($this->filter);
    	}
        return PaymentMade::export();
    }
}
