<?php

use Illuminate\Support\Facades\Route;
use Team64j\LaravelManager\Http\Controllers\AuthController;
use Team64j\LaravelManager\Http\Controllers\BootstrapController;

Route::name('manager.')
    ->prefix('manager')
    ->group(
        fn() => [
            Route::group([
                'prefix' => 'auth',
            ], fn() => [
                Route::post('login', [AuthController::class, 'login']),
                Route::post('logout', [AuthController::class, 'logout']),
                Route::post('refresh', [AuthController::class, 'refresh']),
                Route::get('user', [AuthController::class, 'user']),
            ]),

            Route::get('login', [AuthController::class, 'index'])
                ->name('login'),

            Route::get('/', [BootstrapController::class, 'index'])
                ->middleware('manager.auth')
                ->name('dashboard'),

            Route::get('{any}', [BootstrapController::class, 'index'])
                ->middleware('manager.auth')
                ->where('any', '.*'),
        ]
    );
