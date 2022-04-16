<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Currency;


class CurrencyController extends AdminController
{
    protected $title = 'currencies';
	protected $model = Currency::class;

	public function createForm()
    {
        return [
            'question' => '',
            'show_on_pricing' => 0,
            'answer' => '',
        ];
    }
}
