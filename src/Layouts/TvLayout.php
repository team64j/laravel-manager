<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

use Team64j\LaravelEvolution\Models\SiteTmplvar;
use Illuminate\Support\Facades\Lang;

class TvLayout
{
    /**
     * @param SiteTmplvar $tv
     *
     * @return array
     */
    public function default(SiteTmplvar $tv): array
    {
        $actions = ['cancel', 'saveAnd'];

        if ($tv->id) {
            $actions[] = 'delete';
            $actions[] = 'copy';
        }

        $actions = [
            'component' => 'ActionsButtons',
            'attrs' => [
                'data' => $actions,
            ],
        ];

        $title = [
            'component' => 'Title',
            'model' => 'name',
            'attrs' => [
                'help' => Lang::get('global.tmplvars_msg'),
                'id' => $tv->id ?: null,
            ],
        ];

        $tabs = [
            'component' => 'Tabs',
            'attrs' => [
                'id' => 'tv',
                'data' => [
                    [
                        'id' => 'default',
                        'name' => Lang::get('global.page_data_general'),
                        'class' => 'px-4 py-8 flex flex-wrap',
                        'active' => true,
                    ],
                    [
                        'id' => 'settings',
                        'name' => Lang::get('global.edit_settings'),
                        'class' => 'px-4 py-8 flex flex-wrap',
                    ],
                    [
                        'id' => 'props',
                        'name' => Lang::get('global.settings_properties'),
                        'class' => 'px-4 py-8 flex flex-wrap',
                    ],
                    [
                        'id' => 'templates',
                        'name' => Lang::get('global.templates'),
                        'class' => 'px-4 py-8 flex flex-wrap',
                    ],
                    [
                        'id' => 'roles',
                        'name' => Lang::get('global.role_management_title'),
                        'class' => 'px-4 py-8 flex flex-wrap',
                    ],
                    [
                        'id' => 'permissions',
                        'name' => Lang::get('global.access_permissions'),
                        'class' => 'px-4 py-8 flex flex-wrap',
                    ],
                ],
            ],
        ];

        $category = [
            [
                'key' => $tv->category,
                'value' => $tv->categories ? $tv->categories->category : Lang::get('global.no_category'),
                'selected' => true,
            ],
        ];

        $types = $tv->parameterType((string) $tv->type);

        $tab = [
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'flex flex-wrap md:basis-2/3 xl:basis-9/12 px-4 pb-0',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Fields/Input',
                            'model' => 'name',
                            'attrs' => [
                                'label' => Lang::get('global.tmplvars_name'),
                                'required' => true,
                            ],
                        ],
                        [
                            'component' => 'Fields/Input',
                            'model' => 'caption',
                            'attrs' => [
                                'label' => Lang::get('global.tmplvars_caption'),
                            ],
                        ],
                        [
                            'component' => 'Fields/Textarea',
                            'model' => 'description',
                            'attrs' => [
                                'label' => Lang::get('global.tmplvars_description'),
                                'rows' => 2,
                            ],
                        ],
                        [
                            'component' => 'CodeEditor',
                            'model' => 'elements',
                            'attrs' => [
                                'label' => Lang::get('global.tmplvars_elements'),
                                'help' => Lang::get('global.tmplvars_binding_msg'),
                                'rows' => 2,
                                'config' => [
                                    [
                                        'component' => 'Codemirror',
                                        'name' => 'Codemirror',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'component' => 'CodeEditor',
                            'model' => 'default_text',
                            'attrs' => [
                                'label' => Lang::get('global.tmplvars_default'),
                                'help' => Lang::get('global.tmplvars_binding_msg'),
                                'rows' => 2,
                                'config' => [
                                    [
                                        'component' => 'Codemirror',
                                        'name' => 'Codemirror',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'component' => 'Fields/Select',
                            'model' => 'display',
                            'attrs' => [
                                'label' => Lang::get('global.tmplvars_widget'),
                                'data' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'flex flex-wrap md:basis-1/3 xl:basis-3/12 w-full px-4 pb-0',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Fields/Select',
                            'model' => 'category',
                            'attrs' => [
                                'label' => Lang::get('global.existing_category'),
                                'url' => 'api/category/select',
                                'data' => $category,
                                'itemNew' => 'newcategory',
                            ],
                        ],
                        [
                            'component' => 'Fields/Select',
                            'model' => 'type',
                            'attrs' => [
                                'label' => Lang::get('global.tmplvars_type'),
                                'url' => 'api/tv/types',
                                'data' => $types,
                            ],
                        ],
                        [
                            'component' => 'Fields/Input',
                            'model' => 'rank',
                            'attrs' => [
                                'label' => Lang::get('global.tmplvars_rank'),
                            ],
                        ],
                        [
                            'component' => 'Fields/Checkbox',
                            'model' => 'locked',
                            'attrs' => [
                                'label' => Lang::get('global.lock_tmplvars_msg'),
                                'trueValue' => 1,
                                'falseValue' => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $tabs['slots']['default'] = $tab;

        return [
            $actions,
            $title,
            $tabs,
        ];
    }
}
