<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Team64j\LaravelEvolution\Models\Permissions;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\UserPermissionRequest;
use Team64j\LaravelManager\Http\Resources\UserPermissionResource;
use Team64j\LaravelManager\Traits\PaginationTrait;

class UserPermissionController extends Controller
{
    use PaginationTrait;

    /**
     * @param UserPermissionRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(UserPermissionRequest $request): AnonymousResourceCollection
    {
        $data = Collection::make();
        $filter = $request->get('filter');

        $result = Permissions::query()
            ->with('groups')
            ->when($filter, fn($query) => $query->where('key', 'like', '%' . $filter . '%'))
            ->orderBy('id')
            ->paginate(Config::get('global.number_of_results'));

        /** @var Permissions $item */
        foreach ($result->items() as $item) {
            if (!$data->has($item->group_id)) {
                if ($item->group_id) {
                    $data[$item->group_id] = [
                        'id' => $item->group_id,
                        'name' => Lang::get('global.' . $item->groups->lang_key),
                        'data' => Collection::make(),
                    ];
                } else {
                    $data[0] = [
                        'id' => 0,
                        'name' => Lang::get('global.no_category'),
                        'data' => Collection::make(),
                    ];
                }
            }

            $item->name = Lang::get('global.' . $item->lang_key);

            $data[$item->group_id]['data']->add($item->withoutRelations());
        }

        return UserPermissionResource::collection([
            'data' => $data,
            'pagination' => $this->pagination($result),
        ]);
    }
}
