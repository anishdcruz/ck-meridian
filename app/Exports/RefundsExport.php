<?php

namespace App\Exports;

use App\Tenant\Refund\Refund;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RefundsExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            __('app.refund_date'),
            __('app.name'),
            __('app.organization'),
            __('app.payment'),
            __('app.refund_id'),
            __('app.status'),
            __('app.reason'),
            __('app.amount'),
            __('app.description'),
            __('app.created_at')
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->number,
            formatDate($payment->refund_date),
            $payment->contact->name,
            $payment->contact->organization,
            $payment->payment->number,
            $payment->refund_id,
            $payment->status,
            __('app.'.$payment->reason),
            formatMoney($payment->amount, false),
            $payment->description,
            formatDate($payment->created_at),
        ];
    }

    public function collection()
    {
    	if($this->filter) {
    		return Refund::serverExport($this->filter);
    	}
        return Refund::export();
    }
}
