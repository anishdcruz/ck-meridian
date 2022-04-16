<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
    	// for admin
    	if ($guard == "admin" && Auth::guard($guard)->check()) {
            return redirect()->route('admin.index');
        }

        // for app
        if (Auth::check()) {
            return redirect()->route('app.index');
        }

        return $next($request);
    }
}
