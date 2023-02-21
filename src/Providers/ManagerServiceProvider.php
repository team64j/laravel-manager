<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Providers;

use Illuminate\Support\ServiceProvider;
use Team64j\LaravelManager\Http\Middleware\Authenticate;

class ManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/auth.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        //$this->mergeConfigFrom(__DIR__ . '/../../config/auth.php', 'auth');

        $this->app['config']['auth'] = array_merge_recursive(
            require __DIR__ . '/../../config/auth.php',
            $this->app['config']['auth']
        );

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'manager');

        $this->app['router']->aliasMiddleware(
            'manager.auth',
            Authenticate::class
        );
    }
}
