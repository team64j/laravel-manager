<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use App\Models\PermissionsGroups;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\UserCategoryRequest;
use Team64j\LaravelManager\Http\Resources\UserCategoryResource;
use Team64j\LaravelManager\Traits\PaginationTrait;

class UserCategoryController extends Controller
{
    use PaginationTrait;

    /**
     * @param UserCategoryRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(UserCategoryRequest $request): AnonymousResourceCollection
    {
        $filter = $request->get('filter');

        $result = PermissionsGroups::query()
            ->when($filter, fn($query) => $query->where('name', 'like', '%' . $filter . '%'))
            ->orderBy('id')
            ->paginate(Config::get('global.number_of_results'));

        $data = Collection::make($result->items())
            ->map(function (PermissionsGroups $item) {
                $item->name = Lang::get('global.' . $item->lang_key);

                return $item;
            });

        return UserCategoryResource::collection([
            'data' => $data,
            'pagination' => $this->pagination($result),
        ]);
    }
}
