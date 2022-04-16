<?php

namespace App\Exports;

use App\Tenant\Payment\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PaymentsExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            __('app.invoice'),
            __('app.charge_id'),
            __('app.status'),
            __('app.method'),
            __('app.note'),
            __('app.amount_received'),
            __('app.transaction_fees'),
            __('app.net_amount'),
            __('app.amount_refunded'),
            __('app.available_amount'),
            __('app.created_at')
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->number,
            formatDate($payment->payment_date),
            $payment->contact->name,
            $payment->contact->organization,
            $payment->invoice->number,
            $payment->charge_id,
            $payment->status,
            $payment->method,
            $payment->note,
            formatMoney($payment->amount_received, false),
            formatMoney($payment->transaction_fees, false),
            formatMoney($payment->net_amount, false),
            formatMoney($payment->amount_refunded, false),
            formatMoney($payment->available_amount, false),
            formatDate($payment->created_at),
        ];
    }

    public function collection()
    {
    	if($this->filter) {
    		return Payment::serverExport($this->filter);
    	}
        return Payment::export();
    }
}
