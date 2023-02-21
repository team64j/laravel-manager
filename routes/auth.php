<?php

use Illuminate\Support\Facades\Route;
use Team64j\LaravelManager\Http\Controllers\AuthController;

Route::name('manager.')
    ->prefix('manager')
    ->group(
        fn() => [
            Route::group([
                'prefix' => 'auth',
            ], fn() => [
                Route::post('login', [AuthController::class, 'login'])->middleware('web'),
                Route::post('logout', [AuthController::class, 'logout']),
                Route::post('refresh', [AuthController::class, 'refresh']),
                Route::get('user', [AuthController::class, 'user']),
            ]),
        ]
    );
