<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Team64j\LaravelManager\Http\Requests\DashboardRequest;
use Team64j\LaravelManager\Http\Resources\DashboardResource;
use Team64j\LaravelManager\Layouts\DashboardLayout;
use Team64j\LaravelManager\Traits\PaginationTrait;

class DashboardController extends Controller
{
    use PaginationTrait;

    /**
     * @param DashboardRequest $request
     * @param DashboardLayout $layout
     *
     * @return AnonymousResourceCollection
     */
    public function index(DashboardRequest $request, DashboardLayout $layout): AnonymousResourceCollection
    {
        return DashboardResource::collection([
            //'widgetDocuments' => $this->getDocuments(),
        ])
            ->additional([
                'meta' => [],
                'layout' => $layout->default(),
            ]);
    }

    /**
     * @param DashboardRequest $request
     *
     * @return Application|Factory|View
     */
    public function show(DashboardRequest $request): View | Factory | Application
    {
        $userAttributes = Auth::user()->attributes;

        return view('dashboard', [
            'user' => [
                'username' => Auth::user()->username,
                'role' => $userAttributes->role,
                'permissions' => $userAttributes->rolePermissions->pluck('permission'),
            ],
            'config' => [
                'site_url' => URL::to('/', [], Config::get('global.server_protocol') == 'https'),
            ],
            'lexicon' => Lang::get('global'),
        ]);
    }

    /**
     * @param DashboardRequest $request
     *
     * @return array[]
     */
    public function getNews(DashboardRequest $request): array
    {
        $data = Cache::remember('cms.dashboard.news', 86400, function () {
            $data = [];

            if (Config::get('global.rss_url_news')) {
                $result = \simplexml_load_string(
                    Http::get(Config::get('global.rss_url_news'))
                        ->body()
                );

                foreach ($result->entry as $item) {
                    $content = strip_tags((string) $item->content);

                    if (strlen($content) > 199) {
                        $content = Str::words($content, 15, '...');
                        $content .= '<br />Read <a href="' . $item->link['href'] . '" target="_blank">more</a>.';
                    }

                    $data[] = [
                        'content.html' => '<a href="' . $item->link['href'] . '" target="_blank">' . $item->title .
                            '</a> - <strong>' .
                            $item->updated . '</strong><div class="text-sm">' . $content . '</div>',
                    ];
                }
            }

            return $data;
        });

        return [
            'data' => [
                'data' => $data,
            ],
        ];
    }

    /**
     * @param DashboardRequest $request
     *
     * @return array[]
     */
    public function getNewsSecurity(DashboardRequest $request): array
    {
        $data = Cache::remember('cms.dashboard.news-security', 86400, function () {
            $data = [];

            if (Config::get('global.rss_url_news')) {
                $result = \simplexml_load_string(
                    Http::get(Config::get('global.rss_url_security'))
                        ->body()
                );

                foreach ($result->entry as $item) {
                    $content = strip_tags((string) $item->content);

                    if (strlen($content) > 199) {
                        $content = Str::words($content, 15, '...');
                        $content .= '<br />Read <a href="' . $item->link['href'] . '" target="_blank">more</a>.';
                    }

                    $data[] = [
                        'content.html' => '<a href="' . $item->link['href'] . '" target="_blank">' . $item->title .
                            '</a> - <strong>' .
                            $item->updated . '</strong><div class="text-sm">' . $content . '</div>',
                    ];
                }
            }

            return $data;
        });

        return [
            'data' => [
                'data' => $data,
            ],
        ];
    }

    /**
     * @param DashboardRequest $request
     * @param DashboardLayout $layout
     *
     * @return AnonymousResourceCollection
     */
    public function getSidebar(DashboardRequest $request, DashboardLayout $layout): AnonymousResourceCollection
    {
        return DashboardResource::collection([
            'data' => [],
            'meta' => [],
            'layout' => $layout->sidebar(),
        ]);
    }
}
