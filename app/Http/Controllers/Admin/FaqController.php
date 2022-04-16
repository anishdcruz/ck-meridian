<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Faq;


class FaqController extends AdminController
{
    protected $title = 'faqs';
	protected $model = Faq::class;

	public function createForm()
    {
        return [
            'question' => '',
            'show_on_pricing' => 0,
            'answer' => '',
        ];
    }
}
