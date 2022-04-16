<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade as IlluminateFacades;

class TeamManager extends IlluminateFacades {

   	protected static function getFacadeAccessor()
   	{
   		return 'team_manager';
   	}
}