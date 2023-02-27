<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Team64j\LaravelManager\Http\Controllers\AuthController;

Route::prefix(Config::get('cms.mgr_dir') . '/auth')
    ->name('manager.auth.')
    ->group(
        fn() => [
            Route::name('login')->post('login', [AuthController::class, 'login'])->middleware('web'),
            Route::name('logout')->post('logout', [AuthController::class, 'logout']),
            Route::name('refresh')->post('refresh', [AuthController::class, 'refresh']),
            Route::name('user')->get('user', [AuthController::class, 'user']),
        ]
    );
