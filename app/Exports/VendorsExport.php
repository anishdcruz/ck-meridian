<?php

namespace App\Exports;

use App\Tenant\Vendor\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VendorsExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            __('app.category'),
            __('app.name'),
            __('app.organization'),
            __('app.title'),
            __('app.department'),
            __('app.phone'),
            __('app.mobile'),
            __('app.fax'),
            __('app.email'),
            __('app.website'),
            __('app.primary_address'),
            __('app.other_address'),
            __('app.total_paid'),
            __('app.created_at')
        ];
    }

    public function map($contact): array
    {
        return [
            $contact->number,
            $contact->category->name,
            $contact->name,
            $contact->organization,
            $contact->title,
            $contact->department,
            $contact->phone,
            $contact->mobile,
            $contact->fax,
            $contact->email,
            $contact->website,
            $contact->primary_address,
            $contact->other_address,
            formatMoney($contact->total_paid, false),
            formatDate($contact->created_at),
        ];
    }

    public function collection()
    {
    	if($this->filter) {
    		return Vendor::serverExport($this->filter);
    	}
        return Vendor::export();
    }
}
