<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class AccountSetupController extends Controller
{
    public function show()
    {
    	$team = auth()->user()->currentTeam();
    	$general = [
    		'name' => $team->name,
    		'date_format' => $team->date_format,
    		'timezone' => $team->timezone,
    		'currency_id' => $team->currency_id,
    		'lang_id' => $team->lang_id,
    		'enable_discount' => team_config('enable_discount')
    	];

    	$company = team_configs([
			'company_name',
			'company_address',
			'company_email',
			'company_telephone',
			'company_fax',
			'company_logo',
		]);

		$tax = team_configs([
			'registration_label',
			'tax_enable',
			'tax_label',
			'tax_rate',
			'tax_2_enable',
			'tax_2_label',
			'tax_2_rate',
			'company_tax_id'
		]);

		return [
			'form' => array_merge($general, $company, $tax),
			'options' => [
				'date_formats' => config('meridian.date_formats'),
				'timezones' => config('timezone'),
				'currencies' => DB::table('currencies')->orderBy('name')->get(),
				'langs' => config('meridian.langs')
			]
		];
    }
}
