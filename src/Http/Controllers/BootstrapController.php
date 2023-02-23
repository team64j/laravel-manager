<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Team64j\LaravelManager\Http\Requests\BootstrapRequest;
use Team64j\LaravelManager\Http\Resources\BootstrapResource;

class BootstrapController  extends Controller
{
    /**
     * @param BootstrapRequest $request
     *
     * @return BootstrapResource
     */
    public function index(BootstrapRequest $request): BootstrapResource
    {
        $userAttributes = Auth::user()->attributes;

        return new BootstrapResource([
            'config' => [
                'APP_NAME' => Env::get('APP_NAME'),
                'site_name' => Config::get('global.site_name'),
                'site_url' => URL::to('/', [], Config::get('global.server_protocol') == 'https'),
                'site_start' => (int) Config::get('global.site_start'),
                'error_page' => (int) Config::get('global.error_page'),
                'unauthorized_page' => (int) Config::get('global.unauthorized_page'),
                'site_unavailable_page' => (int) Config::get('global.site_unavailable_page'),
                'remember_last_tab' => (bool) Config::get('global.remember_last_tab'),
                'datetime_format' => Config::get('global.datetime_format'),
                'rb_base_url' => Config::get('global.rb_base_url'),
            ],
            'user' => [
                'username' => Auth::user()->username,
                'role' => $userAttributes->role,
                'permissions' => $userAttributes->rolePermissions->pluck('permission'),
            ],
            'lexicon' => Lang::get('global'),
            'menu' => $this->getMenu(),
        ]);
    }

    /**
     * @return array
     */
    protected function getMenu(): array
    {
        $data = [
            [
                'key' => 'primary',
                'data' => [
                    [
                        'key' => 'toggleSidebar',
                        'icon' => 'fa fa-bars',
                        'click' => 'toggleSidebar',
                    ],
                    [
                        'key' => 'dashboard',
                        'html' => '<span class="block truncate" style="max-width: 10rem;">' .
                            Config::get('global.site_name') .
                            '</span><span class="block text-gray-400 whitespace-nowrap">Evolution CE <span class="text-xs">v' .
                            Config::get('global.settings_version') . '</span></span>',
                        'image' => 'public/resources/img/logo.svg',
                        'class' => 'line-height-1',
                        'to' => [
                            'name' => 'Dashboard',
                        ],
                        'permissions' => ['home'],
                    ],
                    [
                        'key' => 'elements',
                        'name' => Lang::get('global.elements'),
                        'icon' => 'fa fa-th md:hidden',
                        'data' => [
                            [
                                'key' => 'templates',
                                'name' => Lang::get('global.templates'),
                                'icon' => 'fa fa-newspaper',
                                'to' => [
                                    'name' => 'Elements',
                                    'params' => [
                                        'element' => 'templates',
                                    ],
                                ],
                                'url' => 'api/template',
                                'data' => [],
                                'data.to' => 'Template',
                                'data.items' => [
                                    [
                                        'name' => Lang::get('global.new_template'),
                                        'icon' => 'fa fa-plus-circle',
                                        'to' => [
                                            'name' => 'Template',
                                            'params' => [
                                                'id' => null,
                                            ],
                                        ],
                                    ]
                                ],
                                'permissions' => ['new_template', 'edit_template'],
                            ],
                            [
                                'key' => 'tvs',
                                'name' => Lang::get('global.tmplvars'),
                                'icon' => 'fa fa-list-alt',
                                'to' => [
                                    'name' => 'Elements',
                                    'params' => [
                                        'element' => 'tvs',
                                    ],
                                ],
                                'url' => 'api/tv',
                                'data' => [],
                                'data.to' => 'Tv',
                                'data.items' => [
                                    [
                                        'name' => Lang::get('global.new_tmplvars'),
                                        'icon' => 'fa fa-plus-circle',
                                        'to' => [
                                            'name' => 'Tv',
                                            'params' => [
                                                'id' => null,
                                            ],
                                        ],
                                    ]
                                ],
                                'permissions' => ['edit_template', 'edit_snippet', 'edit_chunk', 'edit_plugin'],
                            ],
                            [
                                'key' => 'chunks',
                                'name' => Lang::get('global.htmlsnippets'),
                                'icon' => 'fa fa-th-large',
                                'to' => [
                                    'name' => 'Elements',
                                    'params' => [
                                        'element' => 'chunks',
                                    ],
                                ],
                                'url' => 'api/chunk',
                                'data' => [],
                                'data.to' => 'Chunk',
                                'data.items' => [
                                    [
                                        'name' => Lang::get('global.new_htmlsnippet'),
                                        'icon' => 'fa fa-plus-circle',
                                        'to' => [
                                            'name' => 'Chunk',
                                            'params' => [
                                                'id' => null,
                                            ],
                                        ],
                                    ]
                                ],
                                'permissions' => ['edit_chunk'],
                            ],
                            [
                                'key' => 'snippets',
                                'name' => Lang::get('global.snippets'),
                                'icon' => 'fa fa-code',
                                'to' => [
                                    'name' => 'Elements',
                                    'params' => [
                                        'element' => 'snippets',
                                    ],
                                ],
                                'url' => 'api/snippet',
                                'data' => [],
                                'data.to' => 'Snippet',
                                'data.items' => [
                                    [
                                        'name' => Lang::get('global.new_snippet'),
                                        'icon' => 'fa fa-plus-circle',
                                        'to' => [
                                            'name' => 'Snippet',
                                            'params' => [
                                                'id' => null,
                                            ],
                                        ],
                                    ],
                                ],
                                'permissions' => ['edit_snippet'],
                            ],
                            [
                                'key' => 'plugins',
                                'name' => Lang::get('global.plugins'),
                                'icon' => 'fa fa-plug',
                                'to' => [
                                    'name' => 'Elements',
                                    'params' => [
                                        'element' => 'plugins',
                                    ],
                                ],
                                'url' => 'api/plugin',
                                'data' => [],
                                'data.to' => 'Plugin',
                                'data.items' => [
                                    [
                                        'name' => Lang::get('global.new_plugin'),
                                        'icon' => 'fa fa-plus-circle',
                                        'to' => [
                                            'name' => 'Plugin',
                                            'params' => [
                                                'id' => null,
                                            ],
                                        ],
                                    ],
                                ],
                                'permissions' => ['edit_plugin'],
                            ],
                            [
                                'key' => 'modules',
                                'name' => Lang::get('global.modules'),
                                'icon' => 'fa fa-cubes',
                                'to' => [
                                    'name' => 'Elements',
                                    'params' => [
                                        'element' => 'modules',
                                    ],
                                ],
                                'url' => 'api/module',
                                'data' => [],
                                'data.to' => 'Module',
                                'data.items' => [
                                    [
                                        'name' => Lang::get('global.new_module'),
                                        'icon' => 'fa fa-plus-circle',
                                        'to' => [
                                            'name' => 'Module',
                                            'params' => [
                                                'id' => null,
                                            ],
                                        ],
                                    ],
                                ],
                                'permissions' => ['edit_module'],
                            ],
                            [
                                'key' => 'categories',
                                'name' => Lang::get('global.category_management'),
                                'icon' => 'fa fa-object-group',
                                'to' => [
                                    'name' => 'Elements',
                                    'params' => [
                                        'element' => 'categories',
                                    ],
                                ],
                                'permissions' => ['category_manager'],
                            ],
                            [
                                'key' => 'files',
                                'name' => Lang::get('global.manage_files'),
                                'icon' => 'far fa-folder-open',
                                'to' => [
                                    'name' => 'Files',
                                ],
                                'permissions' => ['file_manager'],
                            ],
                        ],
                    ],
                    [
                        'key' => 'modules',
                        'name' => Lang::get('global.modules'),
                        'icon' => 'fa fa-cubes md:hidden',
                        'url' => 'api/module/exec',
                        'data' => [],
                        'data.to' => 'ModuleExec',
                        'permissions' => ['exec_module'],
                    ],
                    [
                        'key' => 'users',
                        'name' => Lang::get('global.users'),
                        'icon' => 'fa fa-users md:hidden',
                        'data' => [
                            [
                                'key' => 'managers',
                                'name' => Lang::get('global.users'),
                                'icon' => 'fa fa-user-circle',
                                'to' => [
                                    'name' => 'Users',
                                ],
                                'url' => 'api/user',
                                'data' => [],
                                'data.to' => 'User',
                                'permissions' => ['edit_user'],
                            ],
                            [
                                'key' => 'roles',
                                'name' => Lang::get('global.role_management_title'),
                                'icon' => 'fa fa-legal',
                                'to' => [
                                    'name' => 'UsersManagement',
                                    'params' => [
                                        'element' => 'roles',
                                    ],
                                ],
                                'permissions' => ['edit_role'],
                            ],
                            [
                                'key' => 'permissions',
                                'name' => Lang::get('global.web_permissions'),
                                'icon' => 'fa fa-male',
                                'to' => [
                                    'name' => 'UsersGroups',
                                ],
                                'permissions' => ['access_permissions'],
                            ],
                        ],
                    ],
                    [
                        'key' => 'tools',
                        'name' => Lang::get('global.tools'),
                        'icon' => 'fa fa-wrench md:hidden',
                        'data' => [
                            [
                                'key' => 'cache',
                                'name' => Lang::get('global.refresh_site'),
                                'icon' => 'fa fa-recycle',
                                'to' => [
                                    'name' => 'Cache',
                                ],
                                'permissions' => ['empty_cache'],
                            ],
                            //                            [
                            //                                'key' => 'search',
                            //                                'name' => Lang::get('global.search'),
                            //                                'icon' => 'fa fa-search',
                            //                                'to' => [
                            //                                    'name' => 'Search',
                            //                                ],
                            //                            ],
                        ],
                    ],
                ],
            ],

            [
                'key' => 'secondary',
                'data' => [
                    [
                        'key' => 'search',
                        'icon' => 'fa fa-search',
                        'click' => 'toggleSearch',
                        'item.class' => 'justify-center text-sm events-none',
                    ],
                    [
                        'key' => 'theme',
                        'icon' => 'fa fa-sun',
                        'icons' => [
                            'theme' => [
                                [
                                    'key' => 1,
                                    'value' => 'fa fa-sun',
                                ],
                                [
                                    'key' => 2,
                                    'value' => 'fa fa-moon',
                                ],
                            ],
                        ],
                        'click' => 'toggleTheme',
                    ],
                    [
                        'icon' => 'fa fa-desktop relative',
                        'innerIcon' => Config::get('global.site_status') ? null : 'fa fa-triangle-exclamation site-status',
                        'innerTitle' => Config::get('global.site_unavailable_message'),
                        'href' => URL::to('/', [], Config::get('global.server_protocol') == 'https'),
                        'target' => '_blank',
                    ],
                    [
                        'key' => 'account',
                        'icon' => 'far fa-user-circle',
                        'image' => Auth::user()->attributes->photo,
                        'name' => Auth::user()->username,
                        'data' => [
                            [
                                'icon' => 'fa fa-lock',
                                'name' => Lang::get('global.change_password'),
                                'to' => [
                                    'name' => 'PasswordChange',
                                ],
                                'permissions' => ['change_password'],
                            ],
                            [
                                'icon' => 'fa fa-sign-out',
                                'name' => Lang::get('global.logout'),
                                'href' => route('manager.logout'),
                            ],
                        ],
                    ],
                    [
                        'key' => 'settings',
                        'icon' => 'fa fa-cogs',
                        'data' => [
                            [
                                'icon' => 'fa fa-sliders',
                                'name' => Lang::get('global.edit_settings'),
                                'to' => [
                                    'name' => 'Configuration',
                                ],
                                'permissions' => ['settings'],
                            ],
                            [
                                'icon' => 'far fa-calendar',
                                'name' => Lang::get('global.site_schedule'),
                                'to' => [
                                    'name' => 'Schedules',
                                ],
                                'permissions' => ['view_eventlog'],
                            ],
                            [
                                'icon' => 'fa fa-exclamation-triangle',
                                'name' => Lang::get('global.eventlog_viewer'),
                                'to' => [
                                    'name' => 'EventLogs',
                                ],
                                'permissions' => ['view_eventlog'],
                            ],
                            [
                                'icon' => 'fa fa-user-secret',
                                'name' => Lang::get('global.view_logging'),
                                'to' => [
                                    'name' => 'SystemLog',
                                ],
                                'permissions' => ['logs'],
                            ],
                            [
                                'icon' => 'fa fa-info',
                                'name' => Lang::get('global.view_sysinfo'),
                                'to' => [
                                    'name' => 'SystemInfo',
                                ],
                            ],
                            [
                                'icon' => 'far fa-question-circle',
                                'name' => Lang::get('global.help'),
                                'to' => [
                                    'name' => 'Help',
                                ],
                                'permissions' => ['help'],
                            ],
                            [
                                'name' => 'Evolution CE ' . Config::get('global.settings_version'),
                                'item.class' => 'justify-center text-sm events-none',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->checkMenuPermissions($data);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function checkMenuPermissions(array $data): array
    {
        foreach ($data as $k => &$item) {
            if (!empty($item['permissions'])) {
                if (!Auth::user()->hasPermissions($item['permissions'])) {
                    unset($data[$k]);
                }
                unset($item['permissions']);
            } elseif (is_array($item)) {
                $item = $this->checkMenuPermissions($item);
            }

            if (isset($item['data'])) {
                if ($item['data']) {
                    $item['data'] = array_values($item['data']);
                } elseif (empty($item['url'])) {
                    unset($data[$k]);
                }
            }
        }

        return $data;
    }
}
