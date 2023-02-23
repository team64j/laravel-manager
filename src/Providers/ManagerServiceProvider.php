<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Providers;

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Team64j\LaravelEvolution\Models\SystemSetting;
use Team64j\LaravelManager\Http\Middleware\Authenticate;
use Team64j\LaravelManager\Http\Middleware\RedirectIfAuthenticated;

class ManagerServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected string $basePath;

    /**
     * @var bool
     */
    protected bool $isManager;

    public function boot()
    {
        $this->basePath = realpath(__DIR__ . '/../../');
        $this->isManager = str_starts_with($this->app['request']->getPathInfo(), '/manager');

        $this->getRoutes();
        $this->getConfig();
        $this->getLang();
        $this->getView();
        $this->getMiddleware();
        $this->getVite();
    }

    /**
     * @return void
     */
    protected function getRoutes(): void
    {
        $this->loadRoutesFrom($this->basePath . '/routes/auth.php');
        $this->loadRoutesFrom($this->basePath . '/routes/api.php');
        $this->loadRoutesFrom($this->basePath . '/routes/web.php');
    }

    /**
     * @return void
     */
    protected function getConfig(): void
    {
        $dbPrefix = \env('DB_PREFIX');

        if (!is_null($dbPrefix)) {
            Config::set('database.connections.mysql.prefix', $dbPrefix);
            Config::set('database.connections.pgsql.prefix', $dbPrefix);
        }

        if ($this->isManager) {
            Config::set(
                'global',
                (array) Cache::store('file')
                    ->rememberForever(
                        'cms.settings',
                        fn() => SystemSetting::query()
                            ->pluck('setting_value', 'setting_name')
                            ->toArray()
                    )
            );

            $auth = require $this->basePath . '/config/auth.php';

            Config::set('auth.guards.manager', $auth['guards']['manager']);
            Config::set('auth.providers.manager', $auth['providers']['manager']);
            Config::set('auth.defaults.guard', 'manager');
        }
    }

    /**
     * @return void
     */
    protected function getLang(): void
    {
        if ($this->isManager) {
            $this->app->useLangPath($this->basePath . '/lang');

            $this->app->setLocale(
                Str::lower(
                    Str::substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? $this->app['config']['app.locale'], 0, 2)
                )
            );
        }
    }

    /**
     * @return void
     */
    protected function getView(): void
    {
        $this->loadViewsFrom($this->basePath . '/resources/views', 'manager');
    }

    /**
     * @return void
     */
    protected function getMiddleware(): void
    {
        $this->app['router']->aliasMiddleware(
            'manager.guest',
            RedirectIfAuthenticated::class
        );

        $this->app['router']->aliasMiddleware(
            'manager.auth',
            Authenticate::class
        );
    }

    /**
     * @return void
     */
    protected function getVite(): void
    {
        Vite::useHotFile($this->basePath . '/public/hot');

        Vite::useBuildDirectory(
            '..' . str_replace(
                [$this->app->basePath(), DIRECTORY_SEPARATOR],
                ['', '/'],
                $this->basePath
            ) . '/public/build'
        );
    }
}
