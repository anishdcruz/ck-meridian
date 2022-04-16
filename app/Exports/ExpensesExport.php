<?php

namespace App\Exports;

use App\Tenant\Expense\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExpensesExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            __('app.category_id'),
            __('app.name'),
            __('app.organization'),
            __('app.payment_date'),
            __('app.method'),
            __('app.note'),
            __('app.sub_total'),
            __('app.tax'),
            __('app.tax_total'),
            __('app.tax_1'),
            __('app.tax_1_total'),
            __('app.grand_total'),
            __('app.created_at')
        ];
    }

    public function map($exp): array
    {
        return [
            $exp->number,
            $exp->category->name,
            $exp->vendor->name,
            $exp->vendor->organization,
            formatDate($exp->payment_date),
            $exp->method,
            $exp->note,
            formatMoney($exp->sub_total, false),
            $exp->tax,
            formatMoney($exp->tax_total, false),
            $exp->tax_1,
            formatMoney($exp->tax_1_total, false),
            formatMoney($exp->grand_total, false),
            formatDate($exp->created_at),
        ];
    }

    public function collection()
    {
    	if($this->filter) {
    		return Expense::serverExport($this->filter);
    	}
        return Expense::export();
    }
}
