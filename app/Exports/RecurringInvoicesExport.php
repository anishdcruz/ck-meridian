<?php

namespace App\Exports;

use App\Tenant\Invoice\Recurring;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RecurringInvoicesExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            __('app.title'),
            __('app.contact.organization'),
            __('app.contact.name'),
            __('app.next_date'),
            __('app.reference'),
            __('app.sub_total'),
            __('app.discount'),
            __('app.tax'),
            __('app.tax_total'),
            __('app.tax_1'),
            __('app.tax_1_total'),
            __('app.grand_total'),
            __('app.created_at')
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->title,
            $invoice->contact->organization,
            $invoice->contact->name,
            formatDate($invoice->next_date),
            $invoice->reference,
            formatMoney($invoice->sub_total, false),
            formatMoney($invoice->discount, false),
            $invoice->tax,
            formatMoney($invoice->tax_total, false),
            $invoice->tax_1,
            formatMoney($invoice->tax_1_total, false),
            formatMoney($invoice->grand_total, false),
            formatDate($invoice->created_at),
        ];
    }

    public function collection()
    {
    	if($this->filter) {
    		return Recurring::serverExport($this->filter);
    	}
        return Recurring::export();
    }
}
