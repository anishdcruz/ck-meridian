<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade as IlluminateFacades;

class Meridian extends IlluminateFacades {

   	protected static function getFacadeAccessor()
   	{
   		return 'meridian';
   	}
}