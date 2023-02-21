<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controller;

use Illuminate\Routing\Controller;

class BootstrapController  extends Controller
{
    public function index()
    {
        return ['test'];
    }
}
