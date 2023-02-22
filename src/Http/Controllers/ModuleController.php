<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use App\Models\Category;
use App\Models\SiteModule;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\ModuleRequest;
use Team64j\LaravelManager\Http\Resources\ModuleResource;
use Team64j\LaravelManager\Traits\PaginationTrait;
use Throwable;

class ModuleController extends Controller
{
    use PaginationTrait;

    /**
     * @param ModuleRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(ModuleRequest $request): AnonymousResourceCollection
    {
        $filter = $request->get('filter');

        $result = SiteModule::query()
            ->orderBy('name')
            ->where(fn($query) => $filter ? $query->where('name', 'like', '%' . $filter . '%') : null)
            ->whereIn('locked', Auth::user()->attributes->role == 1 ? [0, 1] : [0])
            ->whereIn('disabled', Auth::user()->attributes->role == 1 ? [0, 1] : [0])
            ->paginate(Config::get('global.number_of_results'), [
                'id',
                'name',
                'locked',
                'disabled',
            ]);

        return ModuleResource::collection([
            'data' => [
                'data' => $result->items(),
                'pagination' => $this->pagination($result),
            ],
        ]);
    }

    /**
     * @param ModuleRequest $request
     *
     * @return ModuleResource
     */
    public function store(ModuleRequest $request): ModuleResource
    {
        $module = SiteModule::query()->create($request->validated());

        return new ModuleResource($module);
    }

    /**
     * @param ModuleRequest $request
     * @param string $module
     *
     * @return ModuleResource
     */
    public function show(ModuleRequest $request, string $module): ModuleResource
    {
        return new ModuleResource(SiteModule::query()->findOrNew($module));
    }

    /**
     * @param ModuleRequest $request
     * @param SiteModule $module
     *
     * @return ModuleResource
     */
    public function update(ModuleRequest $request, SiteModule $module): ModuleResource
    {
        $module->update($request->validated());

        return new ModuleResource($module);
    }

    /**
     * @param ModuleRequest $request
     * @param SiteModule $module
     *
     * @return Response
     */
    public function destroy(ModuleRequest $request, SiteModule $module): Response
    {
        $module->delete();

        return response()->noContent();
    }

    /**
     * @param ModuleRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function exec(ModuleRequest $request): AnonymousResourceCollection
    {
        return ModuleResource::collection([
            'data' => [
                'data' => SiteModule::query()
                    ->withoutProtected()
                    ->orderBy('name')
                    ->where('disabled', 0)
                    ->whereIn('locked', Auth::user()->attributes->role == 1 ? [0, 1] : [0])
                    ->whereIn('disabled', Auth::user()->attributes->role == 1 ? [0, 1] : [0])
                    ->get([
                        'id',
                        'name',
                    ]),
            ],
        ]);
    }

    /**
     * @param ModuleRequest $request
     * @param SiteModule $module
     *
     * @return mixed|string
     */
    public function execRun(ModuleRequest $request, SiteModule $module): mixed
    {
        try {
            $code = str_starts_with($module->modulecode, '<?php') ? '//' : '';

            $result = eval($code . $module->modulecode);
        } catch (Throwable $exception) {
            $result = $exception->getMessage();
        }

//        $result = [
//            'data' => [],
//            'layout' => [
//                [
//                    'component' => 'Title'
//                ]
//            ]
//        ];

        return $result;
    }

    /**
     * @param ModuleRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function categories(ModuleRequest $request): AnonymousResourceCollection
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

        $result = SiteModule::query()
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

        /** @var SiteModule $item */
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
                    'icon' => 'fa fa-cube fa-fw',
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

        return ModuleResource::collection([
            'data' => $data,
        ]);
    }

    /**
     * @param ModuleRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function tree(ModuleRequest $request): AnonymousResourceCollection
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
            $result = SiteModule::query()
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

            $result = SiteModule::query()
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
                ->whereHas('modules')
                ->get()
                ->map(function (Category $item) use ($request, $opened) {
                    $data = [
                        'id' => $item->id,
                        'name' => $item->category,
                        'folder' => true,
                    ];

                    if (in_array((int) $item->id, $opened, true)) {
                        $result = $item->modules()
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

        return ModuleResource::collection([
            'data' => $data,
        ]);
    }
}
