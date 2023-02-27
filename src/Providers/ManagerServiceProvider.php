<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Providers;

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
     * @var array
     */
    protected array $middlewareAliases = [
        'manager.guest' => RedirectIfAuthenticated::class,
        'manager.auth' => Authenticate::class,
    ];

    /**
     * @var bool
     */
    protected bool $isManager;

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->registerConfig();

        $this->isManager = str_starts_with($this->app['request']->getPathInfo(), '/' . Config::get('cms.mgr_dir'));
    }

    public function register()
    {
        $this->registerAliases();
        $this->registerMiddlewares();

        $this->booted(function () {
            if (!$this->isManager) {
                return;
            }

            $this->registerRoutes();
            $this->registerLang();
            $this->registerView();
            $this->registerVite();
        });
    }

    protected function registerAliases()
    {
    }

    /**
     * @return void
     */
    protected function registerMiddlewares(): void
    {
        $router = $this->app['router'];

        $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';

        foreach ($this->middlewareAliases as $alias => $middleware) {
            $router->$method($alias, $middleware);
        }
    }

    /**
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/auth.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }

    /**
     * @return void
     */
    protected function registerConfig(): void
    {
        $default = Config::get('database.default');
        $database = require realpath(__DIR__ . '/../../config/database.php');
        $prefix = $database['connections'][$default]['prefix'] ?? null;

        if (!is_null($prefix)) {
            Config::set('database.connections.' . $default . '.prefix', $prefix);
        }

        $path = realpath(__DIR__ . '/../../config/cms.php');
        $this->mergeConfigFrom($path, 'cms');

        $auth = require realpath(__DIR__ . '/../../config/auth.php');
        Config::set('auth.guards.manager', $auth['guards']['manager']);
        Config::set('auth.providers.manager', $auth['providers']['manager']);

        if (!Config::has('global')) {
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
        }
    }

    /**
     * @return void
     */
    protected function registerLang(): void
    {
        $this->app->useLangPath(__DIR__ . '/../../lang');

        $this->app->setLocale(
            Str::lower(
                Str::substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? $this->app['config']['app.locale'], 0, 2)
            )
        );
    }

    /**
     * @return void
     */
    protected function registerView(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'manager');
    }

    /**
     * @return void
     */
    protected function registerVite(): void
    {
        Vite::useHotFile(__DIR__ . '/../../public/hot');

        Vite::useBuildDirectory(
            '..' . str_replace(
                [$this->app->basePath(), DIRECTORY_SEPARATOR],
                ['', '/'],
                realpath(__DIR__ . '/../../')
            ) . '/public/build'
        );
    }
}
