<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade as IlluminateFacades;

class TeamConfig extends IlluminateFacades {

   	protected static function getFacadeAccessor()
   	{
   		return 'team_config';
   	}
}