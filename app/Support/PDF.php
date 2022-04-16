<?php

namespace App\Support;

use Mpdf\Mpdf;
use Auth;
use App\Facades\TeamConfig;
use Mpdf\Output\Destination;

class PDF {

	protected $mpdf;
	protected $team;

	public function __construct()
	{
		$this->mpdf = new Mpdf(config('pdf'));
		$this->team = TeamConfig::getMany([
			'company_tax_id',
			'enable_discount',
			'registration_label',
			'tax_enable',
			'tax_label',
			'tax_2_enable',
			'tax_2_label',
			'company_name',
			'company_address',
			'company_email',
			'company_telephone',
			'company_fax',
			'company_logo'
		]);
	}

	public function preview($view, $data)
	{
		// dd(getBlankLines($data['model']->lines));
		$this->mpdf->WriteHTML(
			view($view, array_merge($data, ['team' => $this->team]))
		);
		if(request('with_copy')) {
			$this->mpdf->WriteHTML('<pagebreak page-selector="letterhead" />');
			$this->mpdf->WriteHTML(
				view($view, array_merge($data, ['team' => $this->team]))
			);
		}

		if(request()->has('mode') && request()->mode == 'preview') {
			return $this->mpdf->Output($data['model']->number, Destination::INLINE);
        }

        return $this->mpdf->Output($data['model']->number, Destination::DOWNLOAD);
	}

	public function string($view, $data)
	{
		$this->mpdf->WriteHTML(
			view($view, array_merge($data, ['team' => $this->team]))
		);

		return $this->mpdf->Output('', 'S');
	}
}