<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use App\Models\Category;
use App\Models\SitePlugin;
use App\Models\SystemEventname;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\PluginRequest;
use Team64j\LaravelManager\Http\Resources\PluginResource;
use Team64j\LaravelManager\Traits\PaginationTrait;

class PluginController extends Controller
{
    use PaginationTrait;

    /**
     * @param PluginRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(PluginRequest $request): AnonymousResourceCollection
    {
        $filter = $request->get('filter');

        $result = SitePlugin::query()
            ->where(fn($query) => $filter ? $query->where('name', 'like', '%' . $filter . '%') : null)
            ->whereIn('locked', Auth::user()->attributes->role == 1 ? [0, 1] : [0])
            ->whereIn('disabled', Auth::user()->attributes->role == 1 ? [0, 1] : [0])
            ->orderBy('name')
            ->paginate(Config::get('global.number_of_results'), [
                'id',
                'name',
                'description',
                'locked',
                'disabled',
                'category',
            ]);

        return PluginResource::collection([
            'data' => [
                'data' => $result->items(),
                'pagination' => $this->pagination($result),
            ],
        ]);
    }

    /**
     * @param PluginRequest $request
     *
     * @return PluginResource
     */
    public function store(PluginRequest $request): PluginResource
    {
        $plugin = SitePlugin::query()->create($request->validated());

        return new PluginResource($plugin);
    }

    /**
     * @param PluginRequest $request
     * @param string $plugin
     *
     * @return PluginResource
     */
    public function show(PluginRequest $request, string $plugin): PluginResource
    {
        return new PluginResource(SitePlugin::query()->findOrNew($plugin));
    }

    /**
     * @param PluginRequest $request
     * @param SitePlugin $plugin
     *
     * @return PluginResource
     */
    public function update(PluginRequest $request, SitePlugin $plugin): PluginResource
    {
        $plugin->update($request->validated());

        return new PluginResource($plugin);
    }

    /**
     * @param PluginRequest $request
     * @param SitePlugin $plugin
     *
     * @return Response
     */
    public function destroy(PluginRequest $request, SitePlugin $plugin): Response
    {
        $plugin->delete();

        return response()->noContent();
    }

    /**
     * @param PluginRequest $request
     *
     * @return PluginResource
     */
    public function sort(PluginRequest $request): PluginResource
    {
        $filter = $request->input('filter');

        $data = [
            'data' => [
                'filter' => true,
                'columns' => [
                    [
                        'name' => '#',
                        'width' => '3rem',
                        'icon' => '<i class="icon fa fa-bars fa-fw draggable-handle"></i>',
                    ],
                    [
                        'name' => 'id',
                        'label' => Lang::get('global.id'),
                        'width' => '5rem',
                        'style' => [
                            'textAlign' => 'right',
                            'fontWeight' => 'bold',
                        ],
                    ],
                    [
                        'name' => 'name',
                        'label' => Lang::get('global.plugin_name'),
                    ],
                    [
                        'name' => 'priority',
                        'label' => Lang::get('global.tmplvars_rank'),
                        'width' => '15rem',
                        'style' => [
                            'textAlign' => 'center',
                        ],
                    ],
                ],
                'data' => SystemEventname::query()
                    ->with(
                        'plugins',
                        fn($q) => $q
                            ->select(['id', 'name', 'disabled', 'priority'])
                            ->where(
                                fn($query) => $filter ? $query->where('name', 'like', '%' . $filter . '%') : null
                            )
                            ->orderBy('pivot_priority')
                    )
                    ->whereHas('plugins')
                    ->orderBy('name')
                    ->get()
                    ->map(function (SystemEventname $item) {
                        $item->setAttribute('data', $item->plugins);
                        $item->setAttribute('draggable', true);

                        return $item->withoutRelations();
                    }),
            ],
        ];

        return new PluginResource($data);
    }

    /**
     * @param PluginRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function categories(PluginRequest $request): AnonymousResourceCollection
    {
        $filter = $request->input('filter');
        $order = $request->input('order', 'category');
        $dir = $request->input('dir', 'asc');
        $fields = ['id', 'name', 'description', 'locked', 'disabled', 'category'];

        $yes = '<span class="text-rose-600">' . Lang::get('global.yes') . '</span>';
        $no = '<span class="text-green-600">' . Lang::get('global.no') . '</span>';

        if (!in_array($order, $fields)) {
            $order = 'id';
        }

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        $result = SitePlugin::query()
            ->select($fields)
            ->with('categories')
            ->where(
                fn($query) => $filter ? $query->where('name', 'like', '%' . $filter . '%') : null
            )
            ->whereIn('locked', Auth::user()->attributes->role == 1 ? [0, 1] : [0])
            ->orderBy($order, $dir)
            ->paginate(Config::get('global.number_of_results'))
            ->appends($request->all());

        $data = Collection::make([
            'data' => Collection::make(),
            'pagination' => $this->pagination($result),
            'sorting' => [
                'order' => $order,
                'dir' => $dir,
            ],
        ]);

        /** @var SitePlugin $item */
        foreach ($result->items() as $item) {
            if (!$data['data']->has($item->category)) {
                if ($item->category) {
                    $data['data'][$item->category] = [
                        'id' => $item->category,
                        'name' => $item->categories->category,
                        'data' => Collection::make(),
                    ];
                } else {
                    $data['data'][0] = [
                        'id' => 0,
                        'name' => Lang::get('global.no_category'),
                        'data' => Collection::make(),
                    ];
                }
            }

            $item->setAttribute('#', [
                'component' => 'HelpIcon',
                'attrs' => [
                    'icon' => 'fa fa-plug fa-fw',
                    'iconInner' => $item->locked ? 'fa fa-lock text-xs' : '',
                    'noOpacity' => true,
                    'fit' => true,
                    'data' => $item->locked ? Lang::get('global.locked') : '',
                ],
            ]);

            $item->setAttribute('category.name', $data['data'][$item->category]['name']);
            $item->setAttribute('description.html', $item->description);

            $data['data'][$item->category]['data']->add($item->withoutRelations());
        }

        $data['data'] = $data['data']->values();

        return PluginResource::collection([
            'data' => $data,
        ]);
    }

    /**
     * @param PluginRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function tree(PluginRequest $request): AnonymousResourceCollection
    {
        $data = [];
        $filter = $request->input('filter');
        $category = $request->integer('parent', -1);
        $fields = ['id', 'name', 'description', 'category', 'locked', 'disabled'];

        $opened = $request->has('opened') ? $request->string('opened')
            ->explode(',')
            ->map(fn($i) => intval($i))
            ->toArray() : [];

        if ($category >= 0) {
            $result = SitePlugin::query()
                ->select($fields)
                ->where('category', $category)
                ->when($filter, fn($query) => $query->where('name', 'like', '%' . $filter . '%'))
                ->orderBy('name')
                ->paginate(Config::get('global.number_of_results'))
                ->appends($request->all());

            $data['data'] = $result->items();
            $data['pagination'] = $this->pagination($result);
        } else {
            $collection = Collection::make();

            $result = SitePlugin::query()
                ->select($fields)
                ->where('category', 0)
                ->paginate(Config::get('global.number_of_results'))
                ->appends($request->all());

            if ($result->count()) {
                $collection->add(
                    [
                        'id' => 0,
                        'name' => Lang::get('global.no_category'),
                        'folder' => true,
                    ] + (in_array(0, $opened, true) ?
                        [
                            'data' => [
                                'data' => $result->items(),
                                'pagination' => $this->pagination($result),
                            ],
                        ]
                        : [])
                );
            }

            $result = Category::query()
                ->whereHas('plugins')
                ->get()
                ->map(function (Category $item) use ($request, $opened) {
                    $data = [
                        'id' => $item->id,
                        'name' => $item->category,
                        'folder' => true,
                    ];

                    if (in_array((int) $item->id, $opened, true)) {
                        $result = $item->plugins()
                            ->paginate(Config::get('global.number_of_results'))
                            ->appends($request->all());

                        $data['data'] = [
                            'data' => $result->items(),
                            'pagination' => $this->pagination($result),
                        ];
                    }

                    $item->setRawAttributes($data);

                    return $item;
                });

            $data['data'] = $collection->merge($result);
        }

        return PluginResource::collection([
            'data' => $data,
        ]);
    }
}
