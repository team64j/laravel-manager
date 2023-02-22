<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Team64j\LaravelManager\Http\Requests\TreeRequest;
use Team64j\LaravelManager\Http\Resources\TreeResource;
use Team64j\LaravelManager\Traits\PaginationTrait;

class TreeController extends Controller
{
    use PaginationTrait;

    /**
     * @var array
     */
    protected array $opened = [];

    /**
     * @param TreeRequest $request
     *
     * @return TreeResource
     */
    public function index(TreeRequest $request): TreeResource
    {
        $data = [];
        $parent = $request->integer('parent');

        $query = DB::table('site_content')
            ->select([
                'id',
                'parent',
                'isfolder',
                'pagetitle',
                'longtitle',
                'menutitle',
                'hidemenu',
                'hide_from_tree',
                'published',
                'deleted',
            ])
            ->where('parent', $parent)
            ->orderBy('menuindex');

        if ($parent) {
            $result = $query
                ->paginate(Config::get('global.number_of_results'))
                ->appends($request->all());

            $data['data'] = $result->items();

            $data['pagination'] = $this->pagination($result);
        } else {
            $data['data'] = $query->get();
        }

        return new TreeResource([
            'data' => $data,
        ]);
    }
}
