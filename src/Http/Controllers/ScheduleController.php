<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Team64j\LaravelManager\Http\Requests\ScheduleRequest;
use Team64j\LaravelManager\Http\Resources\ScheduleResource;

class ScheduleController extends Controller
{
    /**
     * @param ScheduleRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(ScheduleRequest $request): AnonymousResourceCollection
    {
        return ScheduleResource::collection([]);
    }
}
