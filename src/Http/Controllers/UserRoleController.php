<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Team64j\LaravelEvolution\Models\UserRole;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Config;
use Team64j\LaravelManager\Http\Requests\UserRoleRequest;
use Team64j\LaravelManager\Http\Resources\UserRoleResource;
use Team64j\LaravelManager\Traits\PaginationTrait;

class UserRoleController extends Controller
{
    use PaginationTrait;

    /**
     * @param UserRoleRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(UserRoleRequest $request): AnonymousResourceCollection
    {
        $filter = $request->get('filter');

        $result = UserRole::query()
            ->when($filter, fn($query) => $query->where('name', 'like', '%' . $filter . '%'))
            ->orderBy('id')
            ->paginate(Config::get('global.number_of_results'));

        return UserRoleResource::collection([
            'data' => $result->items(),
            'pagination' => $this->pagination($result),
        ]);
    }
}
