<?php

namespace Team64j\LaravelManager\Http\Controllers;

use App\Facades\Uri;
use Team64j\LaravelEvolution\Models\SiteContent;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Team64j\LaravelManager\Http\Requests\DocumentRequest;
use Team64j\LaravelManager\Http\Resources\DocumentResource;
use Team64j\LaravelManager\Layouts\DocumentLayout;
use Team64j\LaravelManager\Traits\PaginationTrait;

class DocumentController extends Controller
{
    use PaginationTrait;

    /**
     * @param DocumentRequest $request
     *
     * @return AnonymousResourceCollection
     * @throws ValidationException
     */
    public function index(DocumentRequest $request): AnonymousResourceCollection
    {
        $fillable = ['id', ...(new SiteContent())->getFillable()];

        $defaultFields = [
            'id',
            'parent',
            'isfolder',
            'pagetitle',
            'longtitle',
            'menutitle',
            'description',
            'menuindex',
            'hidemenu',
            'hide_from_tree',
            'type',
            'published',
            'deleted',
            'editedon',
            'createdon',
        ];

        $limit = min(
            $request->integer('limit', Config::get('global.number_of_results')),
            Config::get('global.number_of_results')
        );
        $order = $request->input('order', 'id');
        $dir = $request->input('dir', 'asc');
        $columns = $request->string('columns')->explode(',');

        $fields = $request->string('fields', implode(',', $defaultFields))->explode(',');
        $additional = $request->string('additional')->explode(',');

        if ($additional->count()) {
            $fields = $fields->merge($additional);
        }

        $fields = $fields
            ->map(fn($i) => trim($i))
            ->intersect($fillable)
            ->filter()
            ->values()
            ->unique()
            ->toArray();

        $this->getValidationFactory()
            ->make(['fields' => $fields], ['fields' => 'required'])
            ->validate();

        if (!in_array($order, $fields)) {
            $order = 'id';
        }

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        foreach ($columns as $key => $column) {
            if (!in_array($column, $fields)) {
                unset($columns[$key]);
                continue;
            }

            $lang = $column;

            if ($lang == 'longtitle') {
                $lang = 'long_title';
            }

            $columns[$key] = [
                'name' => $column,
                'label' => Lang::get('global.' . $lang),
            ];
        }

        $columns = $columns->values();

        $result = SiteContent::query()
            ->orderBy($order, $dir)
            ->where($request->only($fields))
            ->paginate($limit, $fields)
            ->appends($request->all());

        return DocumentResource::collection([
            'data' => [
                'data' => $result->items(),
                'columns' => $columns,
                'pagination' => $this->pagination($result),
            ],
        ]);
    }

    /**
     * @param DocumentRequest $request
     * @param string $document
     * @param DocumentLayout $layout
     *
     * @return DocumentResource
     */
    public function show(DocumentRequest $request, string $document, DocumentLayout $layout): DocumentResource
    {
        /** @var SiteContent $document */
        $document = SiteContent::query()->findOrNew($document);

        if (!$document->id) {
            $document->setRawAttributes([
                'published' => Config::get('global.publish_default'),
                'template' => Config::get('global.default_template'),
                'hide_from_tree' => 0,
                'alias_visible' => 1,
                'richtext' => 1,
                'searchable' => Config::get('global.search_default'),
                'cacheable' => Config::get('global.cache_default'),
                'type' => 'document',
                'contentType' => 'text/html',
                'parent' => 0,
                'content_dispo' => 0,
            ]);
        }

        return $this->getDocument($request, $document, $layout);
    }

    /**
     * @param DocumentRequest $request
     * @param DocumentLayout $layout
     *
     * @return DocumentResource
     */
    public function store(DocumentRequest $request, DocumentLayout $layout): DocumentResource
    {
        /** @var SiteContent $document */
        $document = SiteContent::query()->create($request->validated());

        return $this->getDocument($request, $document, $layout);
    }

    /**
     * @param DocumentRequest $request
     * @param SiteContent $document
     * @param DocumentLayout $layout
     *
     * @return DocumentResource
     */
    public function update(DocumentRequest $request, SiteContent $document, DocumentLayout $layout): DocumentResource
    {
        $document->update($request->validated());

        return $this->getDocument($request, $document, $layout);
    }

    /**
     * @param DocumentRequest $request
     * @param SiteContent $document
     *
     * @return Response
     */
    public function destroy(DocumentRequest $request, SiteContent $document): Response
    {
        $document->delete();

        return response()->noContent();
    }

    /**
     * @param DocumentRequest $request
     * @param SiteContent $document
     * @param DocumentLayout $layout
     *
     * @return DocumentResource
     */
    protected function getDocument(
        DocumentRequest $request,
        SiteContent $document,
        DocumentLayout $layout): DocumentResource
    {
        $data = array_merge(
            $document->withoutRelations()->toArray(),
            ['empty_cache' => 1],
            $this->tvs($document)
        );

        return (new DocumentResource($data))
            ->additional([
                'meta' => $this->getMeta($request, $document),
                'layout' => $layout->default($document),
            ]);
    }

    /**
     * @param DocumentRequest $request
     * @param SiteContent $document
     *
     * @return array
     */
    protected function getMeta(DocumentRequest $request, SiteContent $document): array
    {
        $route = Uri::getRouteById($document->id);

        return [
            'url' => $route['url'] ?? '',
        ];
    }

    /**
     * @param SiteContent $document
     *
     * @return array
     */
    protected function tvs(SiteContent $document): array
    {
        $tvs = $document->getTvs();
        $data = [];

        foreach ($tvs as $tv) {
            $value = $tv['value'];

            switch ($tv['type']) {
                case 'radio':
                case 'checkbox':
                case 'listbox-multiple':
                    if ($tv['elements']) {
                        $value = $tv['value'] == '' ? [] : explode('||', $value);
                    }

                    break;

                default:
            }

            $data[$tv['name']] = $value;
        }

        return $data;
    }

    /**
     * @param DocumentRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function tree(DocumentRequest $request): AnonymousResourceCollection
    {
        $page = $request->integer('page', 1);
        $parent = $request->integer('parent');
        $order = $request->input('order', 'id');
        $dir = $request->input('dir', 'asc');
        $opened = $request->has('opened') ? $request->string('opened')
            ->explode(',')
            ->map(fn($i) => intval($i))
            ->toArray() : [];

        $fields = [
            'id',
            'parent',
            'pagetitle',
            'longtitle',
            'menutitle',
            'isfolder',
            'alias',
            'template',
            'richtext',
            'menuindex',
            'hidemenu',
            'hide_from_tree',
            'type',
            'published',
            'deleted',
            'editedon',
            'createdon',
            'searchable',
            'cacheable',
        ];

        if (!in_array($order, $fields)) {
            $order = 'id';
        }

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        $result = SiteContent::query()
            ->select($fields)
            ->where('parent', $parent)
            ->orderBy($order, $dir)
            ->paginate(Config::get('global.number_of_results'), ['*'], 'page', $page)
            ->appends($request->all());

        $result->map(function ($item) use ($opened, $request) {
            if (in_array($item->id, $opened, true)) {
                $request = clone $request;
                $request->query->set('parent', $item->id);
                $request->query->set('page', 1);
                $result = $this->tree($request);
                $item->setAttribute('data', $result['data']);
            }

            return $item;
        });

        return DocumentResource::collection([
            'data' => [
                'data' => $result->items(),
                'pagination' => $this->pagination($result),
            ],
        ]);
    }

    /**
     * @param DocumentRequest $request
     * @param int $id
     *
     * @return DocumentResource
     */
    public function parents(DocumentRequest $request, int $id): DocumentResource
    {
        return new DocumentResource(Uri::getParentsById($id));
    }
}
