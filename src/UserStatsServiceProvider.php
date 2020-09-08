<?php 

namespace HamzaDjebiri\Laraveluserstats;

use Illuminate\Support\ServiceProvider;

class UserStatsServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->publishes(
        [
           __DIR__.'/path/to/config/userstats.php' => config_path('userstats.php')
        ]);

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations/');

        $this->app->bind('PageStatistics', function()
        {
            return new Http\Controllers\PageStatisticsController;
        });

        $this->app->bind('UserStatistics', function()
        {
            return new Http\Controllers\UserStatisticsController;
        });
    }

    public function register()
    {

    }
}