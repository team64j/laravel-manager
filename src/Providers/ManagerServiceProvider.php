<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Providers;

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Team64j\LaravelManager\Http\Middleware\Authenticate;

class ManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $root = realpath(__DIR__ . '/../../');

        Env::getRepository()->set('ASSET_URL', 'public');

        /**
         * Routes
         */
        $this->loadRoutesFrom($root . '/routes/auth.php');
        $this->loadRoutesFrom($root . '/routes/web.php');
        $this->loadRoutesFrom($root . '/routes/api.php');

        /**
         * Config
         */
        $this->mergeConfigFrom($root . '/config/global.php', 'global');

        $this->app['config']['auth'] = array_merge_recursive(
            require $root . '/config/auth.php',
            $this->app['config']['auth']
        );

        /**
         * Lang
         */
        if (str_starts_with($this->app['request']->getPathInfo(), '/manager')) {
            $this->app->useLangPath($root . '/lang');

            $this->app->setLocale(
                Str::lower(
                    Str::substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? $this->app['config']['app.locale'], 0, 2)
                )
            );
        }

        /**
         * View
         */
        $this->loadViewsFrom($root . '/resources/views', 'manager');

        /**
         * Middleware
         */
        $this->app['router']->aliasMiddleware(
            'manager.auth',
            Authenticate::class
        );

        /**
         * Vite
         */
        Vite::useHotFile($root . '/public/hot');
        Vite::useBuildDirectory(
            '..' . str_replace(
                [$this->app->basePath(), DIRECTORY_SEPARATOR],
                ['', '/'],
                $root
            ) . '/public/build'
        );
    }
}
