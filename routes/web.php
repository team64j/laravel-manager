<?php

use Illuminate\Support\Facades\Route;
use Team64j\LaravelManager\Http\Controllers\AuthController;

$basePath = str_replace([base_path(), DIRECTORY_SEPARATOR], ['', '/'], dirname(__DIR__, 3)) . '/';

Route::prefix('manager')
    ->middleware('web')
    ->group(fn() => [
        Route::middleware('guest:web')
            ->group(fn() => [
                Route::get('login', [AuthController::class, 'formLogin'])->name('manager.login'),
                Route::get('forgot', [AuthController::class, 'formForgot'])->name('manager.forgot'),
            ]),

        //        Route::any('logout', [AuthController::class, 'logout'])
        //            ->middleware('manager.auth:web')
        //            ->name('logout'),
        //
        //        Route::any('/web/module/exec/{module}', [ModuleController::class, 'execRun'])
        //            ->middleware('manager.auth:web')
        //            ->name('moduleExec'),

        Route::view('/', 'manager::manager', [
            'basePath' => $basePath,
        ])
            ->middleware('manager.auth:web')
            ->name('manager.dashboard'),

        Route::view('/{any}', 'manager::manager', [
            'basePath' => $basePath,
        ])
            ->middleware('manager.auth:web')
            ->where('any', '.*'),
    ]);
