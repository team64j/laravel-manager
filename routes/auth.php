<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Team64j\LaravelManager\Http\Controllers\AuthController;

Route::prefix(Config::get('cms.mgr_dir') . '/auth')
    ->name('manager.')
    ->group(
        fn() => [
            Route::post('login', [AuthController::class, 'login'])->middleware('web'),
            Route::post('logout', [AuthController::class, 'logout']),
            Route::post('refresh', [AuthController::class, 'refresh']),
            Route::get('user', [AuthController::class, 'user']),
        ]
    );
