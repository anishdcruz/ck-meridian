<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Currency;

class CurrencyController extends Controller
{
    public function search()
    {
    	    $collection = Currency::search();

    		return [
    			'collection' => $collection
    		];
    }
}
