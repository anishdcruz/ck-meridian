<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Arr;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('module', function ($user, $type) {
        	$role = $user->currentTeam()->role();
        	$modules = $role->permissions['modules'];
        	return Arr::get($modules, $type);
        });

        Gate::define('settings', function ($user, $type) {
        	$role = $user->currentTeam()->role();
        	$modules = $role->permissions['settings'];
        	return $modules[$type];
        });
    }
}
