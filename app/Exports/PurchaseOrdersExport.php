<?php

namespace App\Exports;

use App\Tenant\PurchaseOrder\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PurchaseOrdersExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            __('app.vendor.organization'),
            __('app.vendor.name'),
            __('app.issue_date'),
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

    public function map($po): array
    {
        return [
            $po->number,
            $po->vendor->organization,
            $po->vendor->name,
            formatDate($po->issue_date),
            $po->reference,
            formatMoney($po->sub_total, false),
            formatMoney($po->discount, false),
            $po->tax,
            formatMoney($po->tax_total, false),
            $po->tax_1,
            formatMoney($po->tax_1_total, false),
            formatMoney($po->grand_total, false),
            formatMoney($po->amount_paid, false),
            $po->paid_at,
            $po->status->name,
            formatDate($po->created_at),
        ];
    }

    public function collection()
    {
    	if($this->filter) {
    		return PurchaseOrder::serverExport($this->filter);
    	}
        return PurchaseOrder::export();
    }
}
