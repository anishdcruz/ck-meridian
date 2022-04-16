<?php

namespace App\Exports;

use App\Tenant\Item\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ItemsExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            __('app.code'),
            __('app.category'),
            __('app.description'),
            __('app.uom'),
            __('app.unit_price'),
            __('app.created_at')
        ];
    }

    public function map($item): array
    {
        return [
            $item->code,
            $item->category->name,
            $item->description,
            $item->uom->name,
            formatMoney($item->unit_price, false),
            formatDate($item->created_at),
        ];
    }

    public function collection()
    {
    	if($this->filter) {
    		return Item::serverExport($this->filter);
    	}
        return Item::export();
    }
}
