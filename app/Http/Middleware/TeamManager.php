<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use TeamManager as TM;

class TeamManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $current = Auth::user()->currentTeam();

        if($current) {
        	TM::setTeam(
        	    $current
        	);

        	// set team config

        	config([
        	    'app.timezone' => $current->timezone,
        	    'app.locale' => $current->lang_id
        	]);

        	date_default_timezone_set($current->timezone);

        	 return $next($request);
        } else {
        	return redirect()->route('app.no_team');
        }
    }
}
