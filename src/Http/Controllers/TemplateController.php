<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Team64j\LaravelEvolution\Models\Category;
use Team64j\LaravelEvolution\Models\SiteTemplate;
use Team64j\LaravelEvolution\Models\SiteTmplvar;
use Team64j\LaravelEvolution\Models\SiteTmplvarTemplate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\TemplateRequest;
use Team64j\LaravelManager\Http\Resources\TemplateResource;
use Team64j\LaravelManager\Layouts\TemplateLayout;
use Team64j\LaravelManager\Traits\PaginationTrait;

class TemplateController extends Controller
{
    use PaginationTrait;

    /**
     * @param TemplateRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(TemplateRequest $request): AnonymousResourceCollection
    {
        $filter = $request->get('filter');

        $result = SiteTemplate::query()
            ->where(fn($query) => $filter ? $query->where('templatename', 'like', '%' . $filter . '%') : null)
            ->whereIn('locked', Auth::user()->attributes->role == 1 ? [0, 1] : [0])
            ->orderBy('templatename')
            ->paginate(Config::get('global.number_of_results'), [
                'id',
                'templatename as name',
                'templatealias as alias',
                'description',
                'locked',
                'category',
            ])
            ->appends($request->all());

        return TemplateResource::collection([
            'data' => [
                'data' => $result->items(),
                'pagination' => $this->pagination($result),
            ],
        ]);
    }

    /**
     * @param TemplateRequest $request
     * @param TemplateLayout $layout
     *
     * @return TemplateResource
     */
    public function store(TemplateRequest $request, TemplateLayout $layout): TemplateResource
    {
        /** @var SiteTemplate $template */
        $template = SiteTemplate::query()->create($request->validated());

        $tvsTemplates = $request->input('tvs', []);
        foreach ($tvsTemplates as &$tvsTemplate) {
            $tvsTemplate = [
                'tmplvarid' => $tvsTemplate,
                'templateid' => $template->getKey(),
            ];
        }

        SiteTmplvarTemplate::query()->upsert($tvsTemplates, 'tmplvarid');

        $data = array_merge($template->withoutRelations()->toArray(), [
            'tvs' => $template->tvs->pluck('id'),
            'createbladefile' => 0,
        ]);

        return (new TemplateResource($data))->additional([
            'meta' => $this->getMeta($template),
            'layout' => $layout->default($template),
        ]);
    }

    /**
     * @param TemplateRequest $request
     * @param string $template
     * @param TemplateLayout $layout
     *
     * @return TemplateResource
     */
    public function show(TemplateRequest $request, string $template, TemplateLayout $layout): TemplateResource
    {
        /** @var SiteTemplate $template */
        $template = SiteTemplate::query()->findOrNew($template);

        if (!$template->id) {
            $template->setRawAttributes([
                'category' => 0,
                'selectable' => 1,
            ]);
        }

        $data = array_merge($template->withoutRelations()->toArray(), [
            'tvs' => $template->tvs->pluck('id'),
            'createbladefile' => 0,
        ]);

        return (new TemplateResource($data))->additional([
            'meta' => $this->getMeta($template),
            'layout' => $layout->default($template),
        ]);
    }

    /**
     * @param TemplateRequest $request
     * @param SiteTemplate $template
     * @param TemplateLayout $layout
     *
     * @return TemplateResource
     */
    public function update(TemplateRequest $request, SiteTemplate $template, TemplateLayout $layout): TemplateResource
    {
        $template->update($request->validated());

        SiteTmplvarTemplate::query()
            ->where('templateid', $template->getKey())
            ->delete();

        $tvsTemplates = $request->input('tvs', []);
        foreach ($tvsTemplates as &$tvsTemplate) {
            $tvsTemplate = [
                'tmplvarid' => $tvsTemplate,
                'templateid' => $template->getKey(),
            ];
        }

        SiteTmplvarTemplate::query()->upsert($tvsTemplates, 'tmplvarid');

        $data = array_merge($template->withoutRelations()->toArray(), [
            'tvs' => $template->tvs->pluck('id'),
            'createbladefile' => 0,
        ]);

        return (new TemplateResource($data))->additional([
            'meta' => $this->getMeta($template),
            'layout' => $layout->default($template),
        ]);
    }

    /**
     * @param TemplateRequest $request
     * @param SiteTemplate $template
     *
     * @return Response
     */
    public function destroy(TemplateRequest $request, SiteTemplate $template): Response
    {
        $template->delete();

        return response()->noContent();
    }

    /**
     * @param SiteTemplate $template
     *
     * @return array
     */
    protected function getMeta(SiteTemplate $template): array
    {
        $bladeFile = $template->templatealias . '.blade.php';

        return [
            'tvs' => $this->tvs($template),
            'defaultTemplate' => (int) Config::get('global.default_template'),
            'bladeFile' => file_exists(Config::get('view.app') . '/' . $bladeFile) ? '/' . $bladeFile : '',
        ];
    }

    /**
     * @param SiteTemplate $template
     *
     * @return Collection
     */
    protected function tvs(SiteTemplate $template): Collection
    {
        $filter = request()->input('filter');
        $order = request()->input('order', 'attach');
        $dir = request()->input('dir', 'asc');
        $fields = ['id', 'name', 'caption', 'description', 'category', 'rank'];
        $orders = ['attach', 'id', 'name'];
        $tvs = $template->tvs->pluck('id')->toArray();

        if (!in_array($order, $orders)) {
            $order = $orders[0];
        }

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        $query = SiteTmplvar::query()
            ->select($fields)
            ->with('categories');

        if ($filter) {
            $query->where('name', 'like', '%' . $filter . '%');
        }

        if ($order == 'attach') {
            $query->orderByRaw('FIELD(id, "' . implode('", "', $tvs) . '") ' . ($dir == 'asc' ? 'desc' : 'asc'));
        } else {
            $query->orderBy($order, $dir);
        }

        $result = $query
            ->paginate(Config::get('global.number_of_results'))
            ->appends(request()->all());

        $data = Collection::make([
            'data' => Collection::make(),
            'filter' => true,
            'pagination' => $this->pagination($result),
            'sorting' => [
                'order' => $order,
                'dir' => $dir,
            ],
            'columns' => [
                [
                    'name' => 'attach',
                    'label' => Lang::get('global.role_udperms'),
                    'sort' => true,
                    'width' => '4rem',
                    'style' => [
                        'textAlign' => 'center',
                    ],
                ],
                [
                    'name' => 'id',
                    'label' => 'ID',
                    'sort' => true,
                    'width' => '4rem',
                    'style' => [
                        'textAlign' => 'right',
                    ],
                ],
                [
                    'name' => 'name',
                    'label' => Lang::get('global.tmplvars_name'),
                    'sort' => true,
                    'style' => [
                        'fontWeight' => '500',
                    ],
                ],
                [
                    'name' => 'caption',
                    'label' => Lang::get('global.tmplvars_caption'),
                ],
                [
                    'name' => 'rank',
                    'label' => Lang::get('global.tmplvars_rank'),
                    'style' => [
                        'textAlign' => 'center',
                    ],
                ],
            ],
        ]);

        /** @var SiteTmplvar $item */
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

            $item->setAttribute('category.name', $data['data'][$item->category]['name']);

            $item->setAttribute('attach', [
                'component' => 'Fields/Checkbox',
                'model' => 'tvs',
                'attrs' => [
                    'value' => $item->id,
                    'trueValue' => $item->id,
                    //'falseValue' => null
                ],
            ]);

            $data['data'][$item->category]['data']->add($item->withoutRelations());
        }

        $data['data'] = $data['data']->values();

        return $data;
    }

    /**
     * @param TemplateRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function select(TemplateRequest $request): AnonymousResourceCollection
    {
        $selected = $request->integer('selected');

        return TemplateResource::collection(
            Collection::make()
                ->add([
                    'key' => 0,
                    'value' => 'blank',
                    'selected' => 0 == $selected,
                ])
                ->add(
                    Collection::make([
                        'id' => 0,
                        'name' => __('global.no_category'),
                        'rank' => 0,
                        'data' => SiteTemplate::query()
                            ->where('category', 0)
                            ->where('selectable', 1)
                            ->select([
                                'id',
                                'id as key',
                                'templatename as value',
                            ])
                            ->get()
                            ->map(function (SiteTemplate $template) use ($selected) {
                                $template->setAttribute('selected', $template->id == $selected);

                                return $template;
                            }),
                    ]),
                )
                ->merge(
                    Category::query()
                        ->orderBy('rank')
                        ->get(['id', 'category as name', 'rank'])
                        ->map(function (Category $category) use ($selected) {
                            $category->setRelation(
                                'templates',
                                $category->templates()
                                    ->where('selectable', 1)
                                    ->select([
                                        'id',
                                        'id as key',
                                        'templatename as value',
                                    ])
                                    ->get()
                                    ->map(function (SiteTemplate $template) use ($selected) {
                                        $template->setAttribute('selected', $template->id == $selected);

                                        return $template;
                                    })
                            );

                            return array_merge(
                                $category->attributesToArray(),
                                ['data' => $category->templates->toArray()]
                            );
                        })
                )
                ->filter(fn($category) => !empty($category['data']) || !isset($category['rank']))
                ->values()
        );
    }

    /**
     * @param TemplateRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function categories(TemplateRequest $request): AnonymousResourceCollection
    {
        $filter = $request->input('filter');
        $order = $request->input('order', 'category');
        $dir = $request->input('dir', 'asc');
        $fields = ['id', 'templatename', 'templatealias', 'description', 'category', 'locked'];

        if (!in_array($order, $fields)) {
            $order = 'id';
        }

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        $result = SiteTemplate::query()
            ->select($fields)
            ->with('categories')
            ->when($filter, fn($query) => $query->where('templatename', 'like', '%' . $filter . '%'))
            ->whereIn('locked', Auth::user()->attributes->role == 1 ? [0, 1] : [0])
            ->orderBy($order, $dir)
            ->paginate(Config::get('global.number_of_results'))
            ->appends($request->all());

        $data = Collection::make([
            'data' => Collection::make(),
            //'filter' => true,
            //'route' => 'Template',
            'pagination' => $this->pagination($result),
            'sorting' => [
                'order' => $order,
                'dir' => $dir,
            ],
//            'columns' => [
//                [
//                    'name' => '#',
//                    'width' => '3rem',
//                ],
//                [
//                    'name' => 'id',
//                    'label' => Lang::get('global.id'),
//                    'sort' => true,
//                    'width' => '5rem',
//                    'style' => [
//                        'textAlign' => 'right',
//                        'fontWeight' => 'bold',
//                    ],
//                ],
//                [
//                    'name' => 'templatename',
//                    'label' => Lang::get('global.template_name'),
//                    'sort' => true,
//                    'width' => '20rem',
//                    'style' => [
//                        'fontWeight' => '500',
//                    ],
//                ],
//                [
//                    'name' => 'file',
//                    'label' => Lang::get('global.files_management'),
//                    'width' => '5rem',
//                    'style' => [
//                        'textAlign' => 'center',
//                    ],
//                ],
//                [
//                    'name' => 'description',
//                    'label' => Lang::get('global.template_desc'),
//                ],
//                [
//                    'name' => 'category',
//                    'label' => Lang::get('global.category_heading'),
//                    'sort' => true,
//                    'width' => '15rem',
//                ],
//                [
//                    'name' => 'locked',
//                    'label' => Lang::get('global.locked'),
//                    'width' => '10rem',
//                    'style' => [
//                        'textAlign' => 'center',
//                    ],
//                    'values' => [
//                        0 => $no,
//                        1 => $yes,
//                    ],
//                ],
//            ],
        ]);

        $viewPath = Config::get('view.app');
        $viewRelativePath = str_replace(base_path(), '', resource_path('views'));

        /** @var SiteTemplate $item */
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
                    'icon' => $item->id == Config::get('global.default_template') ? 'fa fa-home fa-fw text-blue-500' : 'fa fa-newspaper fa-fw',
                    'iconInner' => $item->locked ? 'fa fa-lock text-xs' : '',
                    'noOpacity' => true,
                    'fit' => true,
                    'data' => $item->locked ? Lang::get('global.locked') : '',
                ],
            ]);

            $item->setAttribute('category.name', $data['data'][$item->category]['name']);

            $file = '/' . $item->templatealias . '.blade.php';

            if (file_exists($viewPath . $file)) {
                $item->setAttribute(
                    'file',
                    [
                        'component' => 'HelpIcon',
                        'attrs' => [
                            'icon' => 'fa-fw far fa-file-code',
                            'noOpacity' => true,
                            'fit' => true,
                            'data' => Lang::get('global.template_assigned_blade_file') . '<br/>' . $viewRelativePath .
                                $file,
                        ],
                    ]
                );
            }

            $data['data'][$item->category]['data']->add($item->withoutRelations());
        }

        $data['data'] = $data['data']->values();

        return TemplateResource::collection([
            'data' => $data,
        ]);
    }

    /**
     * @param TemplateRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function tree(TemplateRequest $request): AnonymousResourceCollection
    {
        $data = [];
        $filter = $request->input('filter');
        $category = $request->integer('parent', -1);
        $opened = $request->has('opened') ? $request->string('opened')
            ->explode(',')
            ->map(fn($i) => intval($i))
            ->toArray() : [];

        $fields = ['id', 'templatename', 'templatealias', 'description', 'category', 'locked'];

        if ($category >= 0) {
            $result = SiteTemplate::query()
                ->select($fields)
                ->where('category', $category)
                ->when($filter, fn($query) => $query->where('templatename', 'like', '%' . $filter . '%'))
                ->orderBy('templatename')
                ->paginate(Config::get('global.number_of_results'))
                ->appends($request->all());

            $data['data'] = $result->items();
            $data['pagination'] = $this->pagination($result);
        } else {
            $collection = Collection::make();

            $result = SiteTemplate::query()
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
                ->whereHas('templates')
                ->get()
                ->map(function (Category $item) use ($request, $opened) {
                    $data = [
                        'id' => $item->id,
                        'name' => $item->category,
                        'folder' => true,
                    ];

                    if (in_array((int) $item->id, $opened, true)) {
                        $result = $item->templates()
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

        return TemplateResource::collection([
            'data' => $data,
        ]);
    }
}
