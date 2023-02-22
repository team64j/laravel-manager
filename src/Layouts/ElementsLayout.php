<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class ElementsLayout
{
    /**
     * @param string $element
     *
     * @return array
     */
    public function default(string $element): array
    {
        $title = [
            'component' => 'Title',
        ];

        $tabs = [
            'component' => 'Tabs',
            'attrs' => [
                'history' => 'element',
                'id' => 'elements',
                'data' => [],
            ],
            'slots' => [],
        ];

        $yes = '<span class="text-rose-600">' . Lang::get('global.yes') . '</span>';
        $no = '<span class="text-green-600">' . Lang::get('global.no') . '</span>';

        if (Auth::user()->hasPermissions(['edit_template', 'new_template'])) {
            $tabs['attrs']['data'][] = [
                'id' => 'templates',
                'name' => Lang::get('global.templates'),
                'icon' => 'fa fa-newspaper',
                'active' => $element == 'templates',
                'class' => 'py-4',
            ];

            $tabs['slots']['templates'] = [
                'component' => 'Elements/Templates',
                'data' => 'data',
                'attrs' => [
                    'id' => 'templates',
                    'route' => 'Template',
                    'filter' => true,
                    'columns' => [
                        [
                            'name' => '#',
                            'width' => '3rem',
                        ],
                        [
                            'name' => 'id',
                            'label' => Lang::get('global.id'),
                            'sort' => true,
                            'width' => '5rem',
                            'style' => [
                                'textAlign' => 'right',
                                'fontWeight' => 'bold',
                            ],
                        ],
                        [
                            'name' => 'templatename',
                            'label' => Lang::get('global.template_name'),
                            'sort' => true,
                            'width' => '20rem',
                            'style' => [
                                'fontWeight' => '500',
                            ],
                        ],
                        [
                            'name' => 'file',
                            'label' => Lang::get('global.files_management'),
                            'width' => '5rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                        ],
                        [
                            'name' => 'description',
                            'label' => Lang::get('global.template_desc'),
                        ],
                        [
                            'name' => 'category',
                            'label' => Lang::get('global.category_heading'),
                            'sort' => true,
                            'width' => '15rem',
                        ],
                        [
                            'name' => 'locked',
                            'label' => Lang::get('global.locked'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                            'values' => [
                                0 => $no,
                                1 => $yes,
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (Auth::user()->hasPermissions(['edit_template', 'edit_snippet', 'edit_chunk', 'edit_plugin'])) {
            $tabs['attrs']['data'][] = [
                'id' => 'tvs',
                'name' => Lang::get('global.tmplvars'),
                'icon' => 'fa fa-list-alt',
                'active' => $element == 'tvs',
                'class' => 'py-4',
            ];

            $tabs['slots']['tvs'] = [
                'component' => 'Elements/Tvs',
                'data' => 'data',
                'attrs' => [
                    'id' => 'tvs',
                    'route' => 'Tv',
                    'filter' => true,
                    'columns' => [
                        [
                            'name' => '#',
                            'width' => '3rem',
                        ],
                        [
                            'name' => 'id',
                            'label' => Lang::get('global.id'),
                            'sort' => true,
                            'width' => '5rem',
                            'style' => [
                                'textAlign' => 'right',
                                'fontWeight' => 'bold',
                            ],
                        ],
                        [
                            'name' => 'name',
                            'label' => Lang::get('global.tmplvars_name'),
                            'sort' => true,
                            'width' => '20rem',
                            'style' => [
                                'fontWeight' => '500',
                            ],
                        ],
                        [
                            'name' => 'caption',
                            'label' => Lang::get('global.tmplvars_caption'),
                            'sort' => true,
                        ],
                        [
                            'name' => 'type',
                            'label' => Lang::get('global.tmplvars_type'),
                            'width' => '10rem',
                        ],
                        [
                            'name' => 'category',
                            'label' => Lang::get('global.category_heading'),
                            'sort' => true,
                            'width' => '15rem',
                        ],
                        [
                            'name' => 'locked',
                            'label' => Lang::get('global.locked'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                            'values' => [
                                0 => $no,
                                1 => $yes,
                            ],
                        ],
                        [
                            'name' => 'rank',
                            'label' => Lang::get('global.tmplvars_rank'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (Auth::user()->hasPermissions(['edit_chunk', 'new_chunk'])) {
            $tabs['attrs']['data'][] = [
                'id' => 'chunks',
                'name' => Lang::get('global.htmlsnippets'),
                'icon' => 'fa fa-th-large',
                'active' => $element == 'chunks',
                'class' => 'py-4',
            ];

            $tabs['slots']['chunks'] = [
                'component' => 'Elements/Chunks',
                'data' => 'data',
                'attrs' => [
                    'id' => 'chunks',
                    'route' => 'Chunk',
                    'filter' => true,
                    'columns' => [
                        [
                            'name' => '#',
                            'width' => '3rem',
                        ],
                        [
                            'name' => 'id',
                            'label' => Lang::get('global.id'),
                            'sort' => true,
                            'width' => '5rem',
                            'style' => [
                                'textAlign' => 'right',
                                'fontWeight' => 'bold',
                            ],
                        ],
                        [
                            'name' => 'name',
                            'label' => Lang::get('global.htmlsnippet_name'),
                            'sort' => true,
                            'width' => '20rem',
                            'style' => [
                                'fontWeight' => '500',
                            ],
                        ],
                        [
                            'name' => 'description',
                            'label' => Lang::get('global.htmlsnippet_desc'),
                        ],
                        [
                            'name' => 'category',
                            'label' => Lang::get('global.category_heading'),
                            'sort' => true,
                            'width' => '15rem',
                        ],
                        [
                            'name' => 'locked',
                            'label' => Lang::get('global.locked'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                            'values' => [
                                0 => $no,
                                1 => $yes,
                            ],
                        ],
                        [
                            'name' => 'disabled',
                            'label' => Lang::get('global.disabled'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                            'values' => [
                                0 => $no,
                                1 => $yes,
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (Auth::user()->hasPermissions(['edit_snippet', 'new_snippet'])) {
            $tabs['attrs']['data'][] = [
                'id' => 'snippets',
                'name' => Lang::get('global.snippets'),
                'icon' => 'fa fa-code',
                'active' => $element == 'snippets',
                'class' => 'py-4',
            ];

            $tabs['slots']['snippets'] = [
                'component' => 'Elements/Snippets',
                'data' => 'data',
                'attrs' => [
                    'id' => 'snippets',
                    'route' => 'Snippet',
                    'filter' => true,
                    'columns' => [
                        [
                            'name' => '#',
                            'width' => '3rem',
                        ],
                        [
                            'name' => 'id',
                            'label' => Lang::get('global.id'),
                            'sort' => true,
                            'width' => '5rem',
                            'style' => [
                                'textAlign' => 'right',
                                'fontWeight' => 'bold',
                            ],
                        ],
                        [
                            'name' => 'name',
                            'label' => Lang::get('global.snippet_name'),
                            'sort' => true,
                            'width' => '20rem',
                            'style' => [
                                'fontWeight' => '500',
                            ],
                        ],
                        [
                            'name' => 'description',
                            'label' => Lang::get('global.snippet_desc'),
                        ],
                        [
                            'name' => 'category',
                            'label' => Lang::get('global.category_heading'),
                            'sort' => true,
                            'width' => '15rem',
                        ],
                        [
                            'name' => 'locked',
                            'label' => Lang::get('global.locked'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                            'values' => [
                                0 => $no,
                                1 => $yes,
                            ],
                        ],
                        [
                            'name' => 'disabled',
                            'label' => Lang::get('global.disabled'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                            'values' => [
                                0 => $no,
                                1 => $yes,
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (Auth::user()->hasPermissions(['edit_plugin', 'new_plugin'])) {
            $tabs['attrs']['data'][] = [
                'id' => 'plugins',
                'name' => Lang::get('global.plugins'),
                'icon' => 'fa fa-plug',
                'active' => $element == 'plugins',
                'class' => 'py-4',
            ];

            $tabs['slots']['plugins'] = [
                'component' => 'Elements/Plugins',
                'data' => 'data',
                'attrs' => [
                    'id' => 'plugins',
                    'route' => 'Plugin',
                    'filter' => true,
                    'columns' => [
                        [
                            'name' => '#',
                            'width' => '3rem',
                        ],
                        [
                            'name' => 'id',
                            'label' => Lang::get('global.id'),
                            'sort' => true,
                            'width' => '5rem',
                            'style' => [
                                'textAlign' => 'right',
                                'fontWeight' => 'bold',
                            ],
                        ],
                        [
                            'name' => 'name',
                            'label' => Lang::get('global.plugin_name'),
                            'sort' => true,
                            'width' => '20rem',
                            'style' => [
                                'fontWeight' => '500',
                            ],
                        ],
                        [
                            'name' => 'description',
                            'label' => Lang::get('global.plugin_desc'),
                        ],
                        [
                            'name' => 'category',
                            'label' => Lang::get('global.category_heading'),
                            'sort' => true,
                            'width' => '15rem',
                        ],
                        [
                            'name' => 'locked',
                            'label' => Lang::get('global.locked'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                            'values' => [
                                0 => $no,
                                1 => $yes,
                            ],
                        ],
                        [
                            'name' => 'disabled',
                            'label' => Lang::get('global.disabled'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                            'values' => [
                                0 => $no,
                                1 => $yes,
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (Auth::user()->hasPermissions(['edit_module', 'new_module'])) {
            $tabs['attrs']['data'][] = [
                'id' => 'modules',
                'name' => Lang::get('global.modules'),
                'icon' => 'fa fa-cubes',
                'active' => $element == 'modules',
                'class' => 'py-4',
            ];

            $tabs['slots']['modules'] = [
                'component' => 'Elements/Modules',
                'data' => 'data',
                'attrs' => [
                    'id' => 'modules',
                    'route' => 'Module',
                    'filter' => true,
                    'columns' => [
                        [
                            'name' => '#',
                            'width' => '3rem',
                        ],
                        [
                            'name' => 'id',
                            'label' => Lang::get('global.id'),
                            'sort' => true,
                            'width' => '5rem',
                            'style' => [
                                'textAlign' => 'right',
                                'fontWeight' => 'bold',
                            ],
                        ],
                        [
                            'name' => 'name',
                            'label' => Lang::get('global.module_name'),
                            'sort' => true,
                            'width' => '20rem',
                            'style' => [
                                'fontWeight' => '500',
                            ],
                        ],
                        [
                            'name' => 'description',
                            'label' => Lang::get('global.module_desc'),
                        ],
                        [
                            'name' => 'category',
                            'label' => Lang::get('global.category_heading'),
                            'sort' => true,
                            'width' => '15rem',
                        ],
                        [
                            'name' => 'locked',
                            'label' => Lang::get('global.locked'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                            'values' => [
                                0 => $no,
                                1 => $yes,
                            ],
                        ],
                        [
                            'name' => 'disabled',
                            'label' => Lang::get('global.disabled'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                            'values' => [
                                0 => $no,
                                1 => $yes,
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (Auth::user()->hasPermissions(['category_manager'])) {
            $tabs['attrs']['data'][] = [
                'id' => 'categories',
                'name' => Lang::get('global.category_management'),
                'icon' => 'fa fa-object-group',
                'active' => $element == 'categories',
                'class' => 'py-4',
            ];

            $tabs['slots']['categories'] = [
                'component' => 'Elements/Categories',
                'data' => 'data',
                'attrs' => [
                    'id' => 'categories',
                    'route' => 'Category',
                    'filter' => true,
                    'columns' => [
                        [
                            'name' => '#',
                            'width' => '3rem',
                        ],
                        [
                            'name' => 'id',
                            'label' => Lang::get('global.id'),
                            'sort' => true,
                            'width' => '5rem',
                            'style' => [
                                'textAlign' => 'right',
                                'fontWeight' => 'bold',
                            ],
                        ],
                        [
                            'name' => 'category',
                            'label' => Lang::get('global.cm_category_name'),
                            'sort' => true,
                            'style' => [
                                'fontWeight' => '500',
                            ],
                        ],
                        [
                            'name' => 'rank',
                            'label' => Lang::get('global.cm_category_position'),
                            'width' => '10rem',
                            'style' => [
                                'textAlign' => 'center',
                            ],
                        ],
                    ],
                ],
            ];
        }

        return [
            $title,
            $tabs,
        ];
    }
}
