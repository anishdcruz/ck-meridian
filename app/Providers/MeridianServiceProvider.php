<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use App\Support\TeamConfig;
use TeamManager;
use Meridian;
use Illuminate\Routing\Router;

class MeridianServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    	$this->setRouteMacros();

        $this->app['db']->extend('team', function ($config, $name) {
            $team = TeamManager::getTeam();

            if ($team) {
                $config['database'] = $team->database;
            }

            return $this->app['db.factory']->make($config, $name);
        });

        $this->app->bind('team_config', function() {
            return new TeamConfig(TeamManager::getTeam());
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Request::macro('team', function () {
            return TeamManager::getTeam();
        });

        // no card up front
        // Meridian::noCardUpFront();
    }

    protected function setRouteMacros()
    {
    	Router::macro('module', function ($resource, $className) {
    		$this->resource($resource, $className);
    		$this->get('exports/'.$resource, $className . '@export');
    		$this->get('search/'.$resource, $className . '@search');
    	});
    }
}
