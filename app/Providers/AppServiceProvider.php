<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\Meridian;
use App\Support\TeamManager;
use Illuminate\Routing\Router;
use DB;
use File;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('meridian', function() {
            return new Meridian;
        });

        $this->app->bind('team_manager', function() {
            return new TeamManager;
        });

        Router::macro('settings', function ($resource, $className) {

            $this->get(
                $resource,
                '\\App\\Http\\Controllers\\'.$className.'@'.$resource.'Get'
            )->name($resource.'.get');

            $this->post(
                $resource,
                '\\App\\Http\\Controllers\\'.$className.'@'.$resource.'Post'
            )->name($resource.'.post');
        });

        // $this->logQueries();
    }

    protected function logQueries()
    {
        if (app()->environment('local')) {
            DB::listen(function ($query) {
                if (preg_match('/^select/', $query->sql)) {
                    // Log::info('sql: ' .  $query->sql);
                    // Also available are $query->bindings and $query->time.
                    File::append(
                        storage_path('/logs/query.log'),
                        $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL
                    );
                }
            });
        }
    }
}
