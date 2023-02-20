<?php

use Illuminate\Support\Facades\Route;
use Team64j\LaravelManager\Http\Controller\BootstrapController;

Route::name('manager.')
    ->prefix('manager')
    ->group(fn() => [
        Route::get('/', [BootstrapController::class, 'index'])
            ->middleware('auth')
            ->name('dashboard'),

        Route::get('/{any}', [BootstrapController::class, 'index'])
            ->middleware('auth')
            ->where('any', '.*'),
    ]);
