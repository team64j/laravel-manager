<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Team64j\LaravelEvolution\Models\Category;
use Team64j\LaravelEvolution\Models\SiteHtmlSnippet;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\ChunkRequest;
use Team64j\LaravelManager\Http\Resources\ChunkResource;
use Team64j\LaravelManager\Traits\PaginationTrait;

class ChunkController extends Controller
{
    use PaginationTrait;

    /**
     * @param ChunkRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(ChunkRequest $request): AnonymousResourceCollection
    {
        $filter = $request->get('filter');

        $result = SiteHtmlSnippet::query()
            ->when($filter, fn($query) => $query->where('name', 'like', '%' . $filter . '%'))
            ->whereIn('locked', Auth::user()->attributes->role == 1 ? [0, 1] : [0])
            ->orderBy('name')
            ->paginate(Config::get('global.number_of_results'), [
                'id',
                'name',
                'description',
                'locked',
                'disabled',
                'category',
            ]);

        return ChunkResource::collection([
            'data' => [
                'data' => $result->items(),
                'pagination' => $this->pagination($result),
            ],
        ]);
    }

    /**
     * @param ChunkRequest $request
     *
     * @return ChunkResource
     */
    public function store(ChunkRequest $request): ChunkResource
    {
        $chunk = SiteHtmlSnippet::query()->create($request->validated());

        return new ChunkResource($chunk);
    }

    /**
     * @param ChunkRequest $request
     * @param string $chunk
     *
     * @return ChunkResource
     */
    public function show(ChunkRequest $request, string $chunk): ChunkResource
    {
        return new ChunkResource(SiteHtmlSnippet::query()->findOrNew($chunk));
    }

    /**
     * @param ChunkRequest $request
     * @param SiteHtmlSnippet $chunk
     *
     * @return ChunkResource
     */
    public function update(ChunkRequest $request, SiteHtmlSnippet $chunk): ChunkResource
    {
        $chunk->update($request->validated());

        return new ChunkResource($chunk);
    }

    /**
     * @param ChunkRequest $request
     * @param SiteHtmlSnippet $chunk
     *
     * @return Response
     */
    public function destroy(ChunkRequest $request, SiteHtmlSnippet $chunk): Response
    {
        $chunk->delete();

        return response()->noContent();
    }

    /**
     * @param ChunkRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function categories(ChunkRequest $request): AnonymousResourceCollection
    {
        $filter = $request->input('filter');
        $order = $request->input('order', 'category');
        $dir = $request->input('dir', 'asc');
        $fields = ['id', 'name', 'description', 'locked', 'disabled', 'category'];

        if (!in_array($order, $fields)) {
            $order = 'id';
        }

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        $result = SiteHtmlSnippet::query()
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

        /** @var SiteHtmlSnippet $item */
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
                    'icon' => 'fa fa-th-large fa-fw',
                    'iconInner' => $item->locked ? 'fa fa-lock text-xs' : '',
                    'noOpacity' => true,
                    'fit' => true,
                    'data' => $item->locked ? Lang::get('global.locked') : '',
                ],
            ]);

            $item->setAttribute('category.name', $data['data'][$item->category]['name']);

            $data['data'][$item->category]['data']->add($item->withoutRelations());
        }

        $data['data'] = $data['data']->values();

        return ChunkResource::collection([
            'data' => $data,
        ]);
    }

    /**
     * @param ChunkRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function tree(ChunkRequest $request): AnonymousResourceCollection
    {
        $data = [];
        $filter = $request->input('filter');
        $category = $request->integer('parent', -1);
        $fields = ['id', 'name', 'description', 'category', 'locked'];

        $opened = $request->has('opened') ? $request->string('opened')
            ->explode(',')
            ->map(fn($i) => intval($i))
            ->toArray() : [];

        if ($category >= 0) {
            $result = SiteHtmlSnippet::query()
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

            $result = SiteHtmlSnippet::query()
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
                ->whereHas('chunks')
                ->get()
                ->map(function (Category $item) use ($request, $opened) {
                    $data = [
                        'id' => $item->id,
                        'name' => $item->category,
                        'folder' => true,
                    ];

                    if (in_array((int) $item->id, $opened, true)) {
                        $result = $item->chunks()
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

        return ChunkResource::collection([
            'data' => $data,
        ]);
    }
}
