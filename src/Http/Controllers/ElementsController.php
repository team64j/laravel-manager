<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Team64j\LaravelManager\Http\Requests\ElementsRequest;
use Team64j\LaravelManager\Http\Resources\ElementsResource;
use Team64j\LaravelManager\Layouts\ElementsLayout;
use Team64j\LaravelManager\Traits\PaginationTrait;

class ElementsController extends Controller
{
    use PaginationTrait;

    /**
     * @param ElementsRequest $request
     * @param ElementsLayout $layout
     *
     * @return AnonymousResourceCollection
     */
    public function index(ElementsRequest $request, ElementsLayout $layout): AnonymousResourceCollection
    {
        return ElementsResource::collection([
            'data' => [],
        ])
            ->additional([
                'layout' => $layout->default(''),
            ]);
    }

    /**
     * @param ElementsRequest $request
     * @param string $element
     * @param ElementsLayout $layout
     *
     * @return ElementsResource
     */
    public function show(ElementsRequest $request, string $element, ElementsLayout $layout): ElementsResource
    {
        $data = match ($element) {
            'templates' => App::call('\Manager\Http\Controllers\TemplateController@categories', [])->collection,
            'tvs' => App::call('\Manager\Http\Controllers\TvController@categories', [])->collection,
            'chunks' => App::call('\Manager\Http\Controllers\ChunkController@categories', [])->collection,
            'snippets' => App::call('\Manager\Http\Controllers\SnippetController@categories', [])->collection,
            'plugins' => App::call('\Manager\Http\Controllers\PluginController@categories', [])->collection,
            'modules' => App::call('\Manager\Http\Controllers\ModuleController@categories', [])->collection,
            'categories' => App::call('\Manager\Http\Controllers\CategoryController@categories', [])->collection,
            default => []
        };

        return (new ElementsResource($data))
            ->additional([
                'layout' => $layout->default($element),
            ]);
    }
}
