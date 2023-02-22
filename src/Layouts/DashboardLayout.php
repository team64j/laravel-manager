<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class DashboardLayout
{
    /**
     * @return array
     */
    public function default(): array
    {
        return [
            $this->getMessages(),
            $this->getWidgets(),
        ];
    }

    /**
     * @return array[]
     */
    public function sidebar(): array
    {
        $tabs = [
            'component' => 'Tabs',
            'attrs' => [
                'id' => 'tree',
                'uid' => 'TREE',
                'class' => 'h-full !bg-cms-800',
                'loadOnce' => true,
                'watch' => true,
                'data' => [],
            ],
            'slots' => [],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'documents',
            'route' => ['Document', 'Documents'],
            'icon' => 'fa fa-sitemap',
            'title' => Lang::get('global.manage_documents'),
            'class' => '!bg-inherit',
            'active' => true,
        ];

        $tabs['slots']['documents'] = [
            'component' => 'Tree',
            'attrs' => [
                'id' => 'documents',
                'route' => 'Document',
                'url' => 'manager/api/document/tree?order=menuindex&dir=asc',
                'keyTitle' => 'pagetitle',
                'aliases' => [
                    'hide_from_tree' => 'hideChildren',
                    'isfolder' => 'folder',
                    'hidemenu' => 'inhidden',
                    'children' => 'data',
                ],
                'icons' => [
                    'default' => 'far fa-file',
                    Config::get('global.unauthorized_page') => 'fa fa-lock text-rose-600',
                    Config::get('global.site_start') => 'fa fa-home text-blue-500',
                    Config::get('global.site_unavailable_page') => 'fa fa-ban text-amber-400',
                    Config::get('global.error_page') => 'fa fa-exclamation-triangle text-rose-600',
                    'reference' => 'fa fa-link',
                ],
                'templates' => [
                    'help' =>
                        '<b>' . Lang::get('global.pagetitle') . '</b>: {pagetitle}' . PHP_EOL .
                        '<b>' . Lang::get('global.id') . '</b>: {id}' . PHP_EOL .
                        '<b>' . Lang::get('global.resource_opt_menu_title') . '</b>: {menutitle}' . PHP_EOL .
                        '<b>' . Lang::get('global.resource_opt_menu_index') . '</b>: {menuindex}' . PHP_EOL .
                        '<b>' . Lang::get('global.alias') . '</b>: {alias}' . PHP_EOL .
                        '<b>' . Lang::get('global.template') . '</b>: {template}' . PHP_EOL .
                        '<b>' . Lang::get('global.resource_opt_richtext') . '</b>: {richtext}' . PHP_EOL .
                        '<b>' . Lang::get('global.page_data_searchable') . '</b>: {searchable}' . PHP_EOL .
                        '<b>' . Lang::get('global.page_data_cacheable') . '</b>: {cacheable}' . PHP_EOL,
                ],
                'contextMenu' => true,
                //'menu' => [],
            ],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'templates',
            'route' => ['Template'],
            'icon' => 'fa fa-newspaper',
            'title' => Lang::get('global.templates'),
            'class' => '!bg-inherit',
        ];

        $tabs['slots']['templates'] = [
            'component' => 'Tree',
            'attrs' => [
                'id' => 'templates',
                'route' => 'Template',
                'url' => 'manager/api/template/tree',
                'category' => true,
                'aliases' => [
                    'name' => 'title',
                    'templatename' => 'title',
                    'locked' => 'private',
                    'category' => 'parent',
                ],
                'icons' => [
                    'default' => 'fa fa-newspaper',
                ],
            ],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'tvs',
            'route' => ['Tv'],
            'icon' => 'fa fa-list-alt',
            'title' => Lang::get('global.tmplvars'),
            'class' => '!bg-inherit',
        ];

        $tabs['slots']['tvs'] = [
            'component' => 'Tree',
            'attrs' => [
                'id' => 'tvs',
                'route' => 'Tv',
                'url' => 'manager/api/tv/tree',
                'category' => true,
                'aliases' => [
                    'name' => 'title',
                    'locked' => 'private',
                    'category' => 'parent',
                ],
                'icons' => [
                    'default' => 'fa fa-list-alt',
                ],
            ],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'chunks',
            'route' => ['Chunk'],
            'icon' => 'fa fa-th-large',
            'title' => Lang::get('global.htmlsnippets'),
            'class' => '!bg-inherit',
        ];

        $tabs['slots']['chunks'] = [
            'component' => 'Tree',
            'attrs' => [
                'id' => 'chunks',
                'route' => 'Chunk',
                'url' => 'manager/api/chunk/tree',
                'category' => true,
                'aliases' => [
                    'name' => 'title',
                    'locked' => 'private',
                    'disabled' => 'deleted',
                ],
                'icons' => [
                    'default' => 'fa fa-th-large',
                ],
            ],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'snippets',
            'route' => ['Snippet'],
            'icon' => 'fa fa-code',
            'title' => Lang::get('global.snippets'),
            'class' => '!bg-inherit',
        ];

        $tabs['slots']['snippets'] = [
            'component' => 'Tree',
            'attrs' => [
                'id' => 'snippets',
                'route' => 'Snippet',
                'url' => 'manager/api/snippet/tree',
                'category' => true,
                'aliases' => [
                    'name' => 'title',
                    'locked' => 'private',
                    'disabled' => 'deleted',
                ],
                'icons' => [
                    'default' => 'fa fa-code',
                ],
            ],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'plugins',
            'route' => 'Plugin',
            'icon' => 'fa fa-plug',
            'title' => Lang::get('global.plugins'),
            'class' => '!bg-inherit',
        ];

        $tabs['slots']['plugins'] = [
            'component' => 'Tree',
            'attrs' => [
                'id' => 'plugins',
                'route' => 'Plugin',
                'url' => 'manager/api/plugin/tree',
                'category' => true,
                'aliases' => [
                    'name' => 'title',
                    'locked' => 'private',
                    'disabled' => 'deleted',
                ],
                'icons' => [
                    'default' => 'fa fa-plug',
                ],
            ],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'modules',
            'route' => ['Module'],
            'icon' => 'fa fa-cubes',
            'title' => Lang::get('global.modules'),
            'class' => '!bg-inherit',
        ];

        $tabs['slots']['modules'] = [
            'component' => 'Tree',
            'attrs' => [
                'id' => 'modules',
                'route' => 'Module',
                'url' => 'manager/api/module/tree',
                'category' => true,
                'aliases' => [
                    'name' => 'title',
                    'locked' => 'private',
                    'disabled' => 'deleted',
                ],
                'icons' => [
                    'default' => 'fa fa-cubes',
                ],
            ],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'categories',
            'route' => 'Category',
            'icon' => 'fa fa-object-group',
            'title' => Lang::get('global.category_management'),
            'class' => '!bg-inherit',
        ];

        $tabs['slots']['categories'] = [
            'component' => 'Tree',
            'attrs' => [
                'id' => 'categories',
                'route' => 'Category',
                'url' => 'manager/api/category/tree?order=category',
                'category' => true,
                'aliases' => [
                    'category' => 'title',
                ],
                'icons' => [
                    'default' => 'fa fa-object-group',
                ],
            ],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'files',
            'route' => 'File',
            'icon' => 'fa fa-folder-open',
            'title' => Lang::get('global.files_files'),
            'class' => '!bg-inherit',
        ];

        $tabs['slots']['files'] = [
            'component' => 'Tree',
            'attrs' => [
                'id' => 'treeFiles',
                'route' => 'File',
                'url' => 'manager/api/file/tree',
                'category' => true,
                'aliases' => [],
                'icons' => [
                    'default' => 'fa fa-ban',
                    'txt' => 'fa fa-code',
                    'css' => 'fa fa-code text-blue-500',
                    'less' => 'fa fa-code text-blue-500',
                    'cass' => 'fa fa-code text-blue-500',
                    'php' => 'fab fa-php text-purple-500',
                    'vue' => 'fab fa-vuejs text-emerald-500',
                    'ts' => 'fa fa-code text-green-500',
                    'mjs' => 'fa fa-code text-green-600',
                    'cjs' => 'fa fa-code text-green-600',
                    'js' => 'fa fa-code text-green-500',
                    'json' => 'fa fa-code text-green-500',
                    'xml' => 'fa fa-code text-green-500',
                    'yml' => 'fa fa-code',
                    'svg' => 'far fa-image',
                    'webp' => 'far fa-image',
                    'jpg' => 'far fa-image',
                    'jpeg' => 'far fa-image',
                    'png' => 'far fa-image',
                    'gif' => 'far fa-image',
                    'lock' => 'fa fa-lock text-rose-500',
                    'bat' => 'fa fa-file-code text-rose-800',
                    'md' => 'fa fa-code',
                    'artisan' => 'fa fa-code text-blue-500',
                    'htaccess' => 'fa fa-code',
                    'gitignore' => 'fab fa-git text-orange-700',
                    'gitattributes' => 'fab fa-git text-orange-700',
                    'env' => 'fa fa-code',
                    'editorconfig' => 'fa fa-code',
                    //'default' => 'far fa-file',
                    //                    'text/html' => 'far fa-file',
                    //                    'text/plain' => 'far fa-file',
                    //                    'text/x-php' => 'far fa-file',
                    //                    'text/x-java' => 'far fa-file',
                    //                    'text/x-js' => 'far fa-file',
                    //                    'text/xml' => 'far fa-file',
                    //                    'application/json' => 'far fa-file',
                ],
            ],
        ];

        return [
            $tabs,
        ];
    }

    /**
     * @return array
     */
    protected function getMessages(): array
    {
        $data = [
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'block alert-warning p-4 mb-3 rounded',
                ],
                'slots' => [
                    'default' =>
                        Lang::get('global.siteunavailable_message_default') .
                        ' ' . Lang::get('global.update_settings_from_language') .
                        '<a href="configuration" class="btn-sm btn-green ml-2">' . Lang::get('global.online') . '</a>',
                ],
            ],
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'block alert-warning p-4 mb-3 rounded',
                ],
                'slots' => [
                    'default' =>
                        '<strong>' . Lang::get('global.configcheck_warning') . '</strong>' .
                        '<br>' . Lang::get('global.configcheck_installer') .
                        '<br><br><i>' . Lang::get('global.configcheck_what') . '</i>' .
                        '<br>' . Lang::get('global.configcheck_installer_msg'),
                ],
            ],
        ];

        return [
            'component' => 'Template',
            'attrs' => [
                'class' => 'pt-6 px-6',
            ],
            'slots' => [
                'default' => $data,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getWidgets(): array
    {
        $data = [
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'grow w-full lg:basis-1/2 px-2 pb-2',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Section',
                            'attrs' => [
                                'label' => Lang::get('global.welcome_title'),
                                'icon' => 'fa fa-home',
                                'class' => 'lg:min-h-[15rem] content-baseline bg-white dark:bg-cms-700 hover:shadow-lg transition',
                            ],
                            'slots' => [
                                'default' => [
                                    '<div class="data"><table>' .
                                    '<tr><td class="w-52">' . Lang::get('global.yourinfo_username') .
                                    '</td><td><strong>' .
                                    Auth::user()->username . '</strong></td></tr>' .
                                    '<tr><td>' . Lang::get('global.yourinfo_role') . '</td><td><strong>' .
                                    Auth::user()->attributes->userRole->name . '</strong></td></tr>' .
                                    '<tr><td>' . Lang::get('global.yourinfo_previous_login') . '</td><td><strong>' .
                                    Auth::user()->attributes->lastlogin . '</strong></td></tr>' .
                                    '<tr><td>' . Lang::get('global.yourinfo_total_logins') . '</td><td><strong>' .
                                    Auth::user()->attributes->logincount . '</strong></td></tr>' .
                                    '</table></div>',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'grow w-full lg:basis-1/2 px-2 pb-2',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Section',
                            'attrs' => [
                                'label' => Lang::get('global.onlineusers_title'),
                                'icon' => 'fa fa-user',
                                'class' => 'lg:min-h-[15rem] content-baseline bg-white dark:bg-cms-700 hover:shadow-lg transition',
                            ],
                            'slots' => [
                                'default' => [
                                    '<div class="mb-4">' . Lang::get('global.onlineusers_message') . '<b>' .
                                    date('H:i:s') . '</b>)</div>',
                                    [
                                        'component' => 'Panel',
                                        'attrs' => [
                                            'id' => 'widgetUsers',
                                            'class' => '-mx-4 -mb-4 pb-4 grow',
                                            'url' => 'manager/api/user/active',
                                            'history' => false,
                                            'route' => 'User',
                                            'data' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'grow w-full px-2 pb-2',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Section',
                            'attrs' => [
                                'label' => Lang::get('global.activity_title'),
                                'icon' => 'fa fa-pencil',
                                'class' => 'hover:shadow-lg bg-white dark:bg-cms-700 transition overflow-hidden',
                            ],
                            'slots' => [
                                'default' => [
                                    [
                                        'component' => 'Panel',
                                        'attrs' => [
                                            'id' => 'widgetDocuments',
                                            'class' => '-m-4 pb-4 grow',
                                            'url' => 'manager/api/document?order=createdon&dir=desc&limit=10&columns=id,pagetitle,longtitle,createdon',
                                            'history' => false,
                                            'route' => 'Document',
                                            'data' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'grow w-full lg:basis-1/2 px-2 pb-2',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Section',
                            'attrs' => [
                                'label' => Lang::get('global.modx_news'),
                                'icon' => 'fa fa-rss',
                                'class' => 'overflow-hidden bg-white dark:bg-cms-700 hover:shadow-lg transition',
                            ],
                            'slots' => [
                                'default' => [
                                    [
                                        'component' => 'Panel',
                                        'attrs' => [
                                            'id' => 'widgetNews',
                                            'class' => '-m-4 grow h-80 overflow-auto',
                                            'url' => 'manager/api/dashboard/news',
                                            'data' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'grow w-full lg:basis-1/2 px-2 pb-2',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Section',
                            'attrs' => [
                                'label' => Lang::get('global.modx_security_notices'),
                                'icon' => 'fa fa-exclamation-triangle',
                                'class' => 'overflow-hidden bg-white dark:bg-cms-700 hover:shadow-lg transition',
                            ],
                            'slots' => [
                                'default' => [
                                    [
                                        'component' => 'Panel',
                                        'attrs' => [
                                            'id' => 'widgetNews',
                                            'class' => '-m-4 grow h-80 overflow-auto',
                                            'url' => 'manager/api/dashboard/news-security',
                                            'data' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return [
            'component' => 'Template',
            'attrs' => [
                'class' => 'flex flex-wrap items-baseline pt-6 px-4',
            ],
            'slots' => [
                'default' => $data,
            ],
        ];
    }
}
