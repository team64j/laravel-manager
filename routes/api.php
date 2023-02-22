<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::name('manager.api.')
    ->prefix('manager/api')
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
                Route::match(
                    [$route['method']],
                    $path->append('/', $route['uri'])->toString(),
                    $route['action']
                );
            }

            Route::apiResource($path->toString(), (string) $controllerClass);
        }
    });
