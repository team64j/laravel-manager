<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use App\Models\Category;
use App\Models\SiteSnippet;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\SnippetRequest;
use Team64j\LaravelManager\Http\Resources\SnippetResource;
use Team64j\LaravelManager\Traits\PaginationTrait;

class SnippetController extends Controller
{
    use PaginationTrait;

    /**
     * @param SnippetRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(SnippetRequest $request): AnonymousResourceCollection
    {
        $filter = $request->get('filter');

        $result = SiteSnippet::query()
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

        return SnippetResource::collection([
            'data' => [
                'data' => $result->items(),
                'pagination' => $this->pagination($result),
            ],
        ]);
    }

    /**
     * @param SnippetRequest $request
     *
     * @return SnippetResource
     */
    public function store(SnippetRequest $request): SnippetResource
    {
        $snippet = SiteSnippet::query()->create($request->validated());

        return new SnippetResource($snippet);
    }

    /**
     * @param SnippetRequest $request
     * @param string $snippet
     *
     * @return SnippetResource
     */
    public function show(SnippetRequest $request, string $snippet): SnippetResource
    {
        return new SnippetResource(SiteSnippet::query()->findOrNew($snippet));
    }

    /**
     * @param SnippetRequest $request
     * @param SiteSnippet $snippet
     *
     * @return SnippetResource
     */
    public function update(SnippetRequest $request, SiteSnippet $snippet): SnippetResource
    {
        $snippet->update($request->validated());

        return new SnippetResource($snippet);
    }

    /**
     * @param SnippetRequest $request
     * @param SiteSnippet $snippet
     *
     * @return Response
     */
    public function destroy(SnippetRequest $request, SiteSnippet $snippet): Response
    {
        $snippet->delete();

        return response()->noContent();
    }

    /**
     * @param SnippetRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function categories(SnippetRequest $request): AnonymousResourceCollection
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

        $result = SiteSnippet::query()
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

        /** @var SiteSnippet $item */
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
                    'icon' => 'fa fa-code fa-fw',
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

        return SnippetResource::collection([
            'data' => $data,
        ]);
    }

    /**
     * @param SnippetRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function tree(SnippetRequest $request): AnonymousResourceCollection
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
            $result = SiteSnippet::query()
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

            $result = SiteSnippet::query()
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
                ->whereHas('snippets')
                ->get()
                ->map(function (Category $item) use ($request, $opened) {
                    $data = [
                        'id' => $item->id,
                        'name' => $item->category,
                        'folder' => true,
                    ];

                    if (in_array((int) $item->id, $opened, true)) {
                        $result = $item->snippets()
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

        return SnippetResource::collection([
            'data' => $data,
        ]);
    }
}
