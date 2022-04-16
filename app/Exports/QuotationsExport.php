<?php

namespace App\Exports;

use App\Tenant\Quotation\Quotation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class QuotationsExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            __('app.expiry_date'),
            __('app.reference'),
            __('app.sub_total'),
            __('app.discount'),
            __('app.tax'),
            __('app.tax_total'),
            __('app.tax_1'),
            __('app.tax_1_total'),
            __('app.grand_total'),
            __('app.status.name'),
            __('app.created_at')
        ];
    }

    public function map($quotation): array
    {
        return [
            $quotation->number,
            $quotation->contact->organization,
            $quotation->contact->name,
            formatDate($quotation->issue_date),
            formatDate($quotation->expiry_date),
            $quotation->reference,
            formatMoney($quotation->sub_total, false),
            formatMoney($quotation->discount, false),
            $quotation->tax,
            formatMoney($quotation->tax_total, false),
            $quotation->tax_1,
            formatMoney($quotation->tax_1_total, false),
            formatMoney($quotation->grand_total, false),
            $quotation->status->name,
            formatDate($quotation->created_at),
        ];
    }

    public function collection()
    {
    	if($this->filter) {
    		return Quotation::serverExport($this->filter);
    	}
        return Quotation::export();
    }
}
