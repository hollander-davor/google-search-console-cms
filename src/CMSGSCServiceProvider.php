<?php

namespace Hoks\CMSGSC;

use Illuminate\Support\ServiceProvider;
use Hoks\CMSGSC\Commands\GetGoogleSearchConsoleData;

class CMSGSCServiceProvider extends ServiceProvider{

    public function boot(){
        //load config---------------------
        $this->publishes([
            __DIR__.'/config/gsc-cms.php' => config_path('gsc-cms.php')
        ],'config');
        //load routes---------------------
        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        //load migrations---------------------
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        //load views---------------------
        $this->publishes([
            __DIR__.'/views/google_search_console' => resource_path('views/google_search_console'),
        ], 'views');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GetGoogleSearchConsoleData::class,
            ]);
        }

    }

    public function register(){
     
        $this->mergeConfigFrom(__DIR__.'/config/gsc-cms.php', 'gsc-cms');
    }
}
