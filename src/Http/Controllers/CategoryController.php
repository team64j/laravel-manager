<?php

namespace Team64j\LaravelManager\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\CategoryRequest;
use Team64j\LaravelManager\Http\Resources\CategoryResource;
use Team64j\LaravelManager\Http\Resources\TemplateResource;
use Team64j\LaravelManager\Traits\PaginationTrait;

class CategoryController extends Controller
{
    use PaginationTrait;

    /**
     * @param CategoryRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(CategoryRequest $request): AnonymousResourceCollection
    {
        $filter = $request->input('filter');

        $result = Category::query()
            ->where(fn($query) => $filter ? $query->where('category', 'like', '%' . $filter . '%') : null)
            ->paginate(Config::get('global.number_of_results'), [
                'id',
                'category as name',
                'rank',
            ]);

        return CategoryResource::collection([
            'data' => [
                'data' => $result->items(),
                'pagination' => $this->pagination($result),
            ],
        ]);
    }

    /**
     * @param CategoryRequest $request
     *
     * @return CategoryResource
     */
    public function store(CategoryRequest $request): CategoryResource
    {
        $category = Category::query()->create($request->validated());

        return new CategoryResource($category);
    }

    /**
     * @param CategoryRequest $request
     * @param string $category
     *
     * @return CategoryResource
     */
    public function show(CategoryRequest $request, string $category): CategoryResource
    {
        return new CategoryResource(Category::query()->findOrNew($category));
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     *
     * @return CategoryResource
     */
    public function update(CategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());

        return new CategoryResource($category);
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     *
     * @return Response
     */
    public function destroy(CategoryRequest $request, Category $category): Response
    {
        $category->delete();

        return response()->noContent();
    }

    /**
     * @param CategoryRequest $request
     *
     * @return array
     */
    public function sort(CategoryRequest $request): array
    {
        return [
            'data' => [
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
                        'name' => 'category',
                        'label' => Lang::get('global.cm_category_name'),
                    ],
                    [
                        'name' => 'rank',
                        'label' => Lang::get('global.cm_category_position'),
                        'width' => '15rem',
                        'style' => [
                            'textAlign' => 'center',
                        ],
                    ],
                ],
                'data' => [
                    [
                        'data' => Category::query()
                            ->orderBy('rank')
                            ->get(),
                        'draggable' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param CategoryRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function select(CategoryRequest $request): AnonymousResourceCollection
    {
        $selected = $request->integer('selected');

        return TemplateResource::collection(
            Collection::make()
                ->add([
                    'key' => 'newcategory',
                    'value' => Lang::get('global.cm_create_new_category'),
                ])
                ->add([
                    'name' => Lang::get('global.category_management'),
                    'data' => Collection::make()
                        ->add(
                            new Category([
                                'key' => 0,
                                'category' => Lang::get('global.no_category'),
                            ])
                        )
                        ->merge(
                            Category::all()
                        )
                        ->map(fn(Category $category) => [
                            'key' => $category->id ?: 0,
                            'value' => $category->category,
                            'selected' => $selected == ($category->id ?: 0),
                        ]),
                ])
        );
    }

    /**
     * @param CategoryRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function categories(CategoryRequest $request): AnonymousResourceCollection
    {
        $filter = $request->input('filter');
        $order = $request->input('order', 'id');
        $dir = $request->input('dir', 'asc');
        $fields = ['id', 'category', 'rank'];

        if (!in_array($order, $fields)) {
            $order = 'id';
        }

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        $result = Category::query()
            ->select($fields)
            ->where(
                fn($query) => $filter ? $query->where('category', 'like', '%' . $filter . '%') : null
            )
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

        /** @var Category $item */
        foreach ($result->items() as $item) {
            if (!$data['data']->has(0)) {
                $data['data'][0] = [
                    'data' => Collection::make(),
                ];
            }

            $item->setAttribute('#', [
                'component' => 'HelpIcon',
                'attrs' => [
                    'icon' => 'fa fa-object-group fa-fw',
                    'noOpacity' => true,
                    'fit' => true,
                ],
            ]);

            $data['data'][0]['data']->add($item->withoutRelations());
        }

        $data['data'] = $data['data']->values();

        return CategoryResource::collection([
            'data' => $data,
        ]);
    }

    /**
     * @param CategoryRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function tree(CategoryRequest $request): AnonymousResourceCollection
    {
        $data = [];
        $filter = $request->input('filter');
        $order = $request->input('order', 'id');
        $dir = $request->input('dir', 'asc');
        $fields = ['id', 'category', 'rank'];

        if (!in_array($order, $fields)) {
            $order = 'id';
        }

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        $result = Category::query()
            ->when($filter, fn($query) => $query->where('category', 'like', '%' . $filter . '%'))
            ->orderByRaw('upper(' . $order . ') ' . $dir)
            ->paginate(Config::get('global.number_of_results'))
            ->appends($request->all());

        $data['data'] = $result->items();
        $data['pagination'] = $this->pagination($result);

        return CategoryResource::collection([
            'data' => $data,
        ]);
    }
}
