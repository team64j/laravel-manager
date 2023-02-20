<?php

use Illuminate\Support\Facades\Route;

Route::name('manager.')
    ->group('api', fn() => [
        Route::any('manager', []),
    ]);
