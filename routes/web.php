<?php

use Illuminate\Support\Facades\Route;
use Team64j\LaravelManager\Http\Controllers\DashboardController;

Route::prefix('manager')
    ->middleware('web')
    ->group(fn() => [
        Route::middleware('guest:web')
            ->group(fn() => [
                Route::view('login', 'manager::login')->name('manager.login'),
                Route::view('forgot', 'manager::forgot')->name('manager.forgot'),
            ]),

//        Route::any('logout', [AuthController::class, 'logout'])
//            ->middleware('manager.auth:web')
//            ->name('logout'),
//
//        Route::any('/web/module/exec/{module}', [ModuleController::class, 'execRun'])
//            ->middleware('manager.auth:web')
//            ->name('moduleExec'),

        Route::get('/', [DashboardController::class, 'index'])
            ->middleware('manager.auth:web')
            ->name('manager.dashboard'),

        Route::get('/{any}', [DashboardController::class, 'index'])
            ->middleware('manager.auth:web')
            ->where('any', '.*'),
    ]);
