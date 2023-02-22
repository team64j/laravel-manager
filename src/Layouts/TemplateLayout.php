<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

use Team64j\LaravelEvolution\Models\SiteTemplate;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class TemplateLayout
{
    public function default(SiteTemplate $template): array
    {
        $actions = ['cancel', 'saveAnd'];

        if ($template->id) {
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
            'model' => 'templatename',
            'attrs' => [
                'help' => Lang::get('global.template_msg'),
                'id' => $template->id ?: null,
            ],
        ];

        $activeDefault = true;
        $activeTvs = false;

        if (request()->has('page')) {
            $activeDefault = false;
            $activeTvs = true;
        }

        $tabs = [
            'component' => 'Tabs',
            'attrs' => [
                'id' => 'template',
                'data' => [
                    [
                        'id' => 'default',
                        'name' => Lang::get('global.settings_general'),
                        'class' => 'px-4 py-8 flex flex-wrap',
                        'active' => $activeDefault,
                    ],
                    [
                        'id' => 'tvs',
                        'name' => Lang::get('global.template_assignedtv_tab'),
                        'class' => 'py-8 flex flex-wrap',
                        'active' => $activeTvs,
                    ],
                ],
            ],
        ];

        $category = [
            [
                'key' => $template->category,
                'value' => $template->categories ? $template->categories->category : Lang::get('global.no_category'),
                'selected' => true,
            ],
        ];

        $bladeFile = Config::get('view.app') . '/' . $template->templatealias . '.blade.php';
        $isBladeFile = file_exists($bladeFile);
        $relativeBladeFile = str_replace(dirname(base_path()), '', $bladeFile);

        $tab = [
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'flex flex-wrap grow md:basis-2/3 xl:basis-9/12 px-4 pb-0 md:pb-4',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Fields/Input',
                            'model' => 'templatename',
                            'attrs' => [
                                'label' => Lang::get('global.template_name'),
                                'requiredText' => Config::get('global.default_template') == $template->id ? trim(Lang::get('global.defaulttemplate_title'), ':') : '',
                                'required' => true,
                            ],
                        ],
                        [
                            'component' => 'Fields/Input',
                            'model' => 'templatealias',
                            'attrs' => [
                                'label' => Lang::get('global.alias'),
                            ],
                        ],
                        [
                            'component' => 'Fields/Textarea',
                            'model' => 'description',
                            'attrs' => [
                                'label' => Lang::get('global.template_desc'),
                                'rows' => 2,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'flex flex-wrap grow md:basis-1/3 xl:basis-3/12 px-4',
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
                            'component' => 'Fields/Checkbox',
                            'model' => 'selectable',
                            'attrs' => [
                                'label' => Lang::get('global.template_selectable'),
                                'trueValue' => 1,
                                'falseValue' => 0,
                            ],
                        ],
                        [
                            'component' => 'Fields/Checkbox',
                            'model' => 'locked',
                            'attrs' => [
                                'label' => Lang::get('global.lock_template_msg'),
                                'trueValue' => 1,
                                'falseValue' => 0,
                            ],
                        ],
                    ],
                ],
            ],
            (
            $isBladeFile
                ?
                '<span class="text-green-600">' . Lang::get('global.template_assigned_blade_file') . ': ' .
                $relativeBladeFile . '</span>'
                :
                [
                    'component' => 'Fields/Checkbox',
                    'model' => 'createbladefile',
                    'attrs' => [
                        'label' => Lang::get('global.template_create_blade_file'),
                        'trueValue' => 1,
                        'falseValue' => 0,
                        'class' => 'px-4',
                    ],
                ]
            ),
            [
                'component' => 'CodeEditor',
                'model' => 'content',
                'attrs' => [
                    'label' => Lang::get('global.template_code'),
                    'class' => 'px-4',
                    'config' => [
                        [
                            'component' => 'Codemirror',
                            'name' => 'Codemirror',
                            'lang' => 'html',
                        ],
                    ],
                ],
            ],
        ];

        $tabs['slots']['default'] = $tab;

        $tab = [
            [
                'component' => 'Panel',
                'data' => 'meta.tvs',
                'attrs' => [
                    'id' => 'templateTvs',
                    'class' => 'grow',
                ],
                'slots' => [
                    'top' => '<div class="font-bold">' . Lang::get('global.template_tv_msg') . '</div>',
                ],
            ],
        ];

        $tabs['slots']['tvs'] = $tab;

        return [
            $actions,
            $title,
            $tabs,
        ];
    }
}
