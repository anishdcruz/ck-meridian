<?php

namespace App\Exports;

use App\Tenant\Invoice\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvoicesExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            __('app.contact.organization'),
            __('app.contact.name'),
            __('app.issue_date'),
            __('app.due_date'),
            __('app.reference'),
            __('app.sub_total'),
            __('app.discount'),
            __('app.tax'),
            __('app.tax_total'),
            __('app.tax_1'),
            __('app.tax_1_total'),
            __('app.grand_total'),
            __('app.amount_paid'),
            __('app.paid_at'),
            __('app.status.name'),
            __('app.created_at')
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->number,
            $invoice->contact->organization,
            $invoice->contact->name,
            formatDate($invoice->issue_date),
            formatDate($invoice->due_date),
            $invoice->reference,
            formatMoney($invoice->sub_total, false),
            formatMoney($invoice->discount, false),
            $invoice->tax,
            formatMoney($invoice->tax_total, false),
            $invoice->tax_1,
            formatMoney($invoice->tax_1_total, false),
            formatMoney($invoice->grand_total, false),
            formatMoney($invoice->amount_paid, false),
            $invoice->paid_at,
            $invoice->status->name,
            formatDate($invoice->created_at),
        ];
    }

    public function collection()
    {
    	if($this->filter) {
    		return Invoice::serverExport($this->filter);
    	}
        return Invoice::export();
    }
}
