<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::prefix(Config::get('cms.mgr_dir') . '/api')
    ->name('manager.api.')
    ->middleware('manager.auth:manager')
    ->group(function (): void {
        $requestsNamespace = '\Team64j\LaravelManager\Http\Requests\\';
        $controllersNamespace = '\Team64j\LaravelManager\Http\Controllers\\';
        $files = File::files(__DIR__ . '/../src/Http/Requests');

        foreach ($files as $file) {
            $requestClass = Str::of($requestsNamespace)
                ->append($file->getRelativePathname())
                ->replace(['/', '.php'], ['\\', '']);

            $className = $requestClass
                ->replaceFirst($requestsNamespace, '')
                ->replaceLast('Request', '');

            $controllerClass = $className
                ->prepend($controllersNamespace)
                ->append('Controller');

            $path = $className->lower();

            $routes = method_exists(
                $requestClass->toString(),
                'getRoutes'
            ) ? App::call($requestClass . '::getRoutes', []) : [];

            foreach ($routes as $route) {
                $routeName = $className->lower() . '.' . ($route['name'] ?? $route['action'][1] ?? '');

                Route::name($routeName)->match(
                    [$route['method']],
                    $path->append('/', $route['uri'])->toString(),
                    $route['action']
                );
            }

            Route::apiResource($path->toString(), (string) $controllerClass);
        }
    });
