<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Providers;

use Illuminate\Support\ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // ... other things
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
    }
}
