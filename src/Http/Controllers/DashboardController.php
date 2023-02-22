<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{

    /**
     * @return Application|Factory|View
     */
    public function index(): View | Factory | Application
    {
        return view('manager::manager', [
            'basePath' => str_replace([base_path(), DIRECTORY_SEPARATOR], ['', '/'], dirname(__DIR__, 3)) . '/'
        ]);
    }
}
