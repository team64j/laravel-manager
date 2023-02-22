<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

use Team64j\LaravelEvolution\Models\SiteContent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class DocumentLayout
{
    /**
     * @param SiteContent $document
     *
     * @return array
     */
    public function default(SiteContent $document): array
    {
        $actions = ['cancel', 'saveAnd'];

        if ($document->deleted) {
            $actions[] = 'restore';
            $actions[] = 'view';
        } elseif ($document->id) {
            $actions[] = 'delete';
            $actions[] = 'copy';
            $actions[] = 'view';
        }

        $actions = [
            'component' => 'ActionsButtons',
            'attrs' => [
                'data' => $actions,
            ],
        ];

        $title = [
            'component' => 'Title',
            'model' => 'pagetitle',
            'attrs' => [
                'id' => $document->id ?: null,
            ],
        ];

        $tabs = [
            'component' => 'Tabs',
            'attrs' => [
                'id' => 'document',
                'vertical' => false,
            ],
            'slots' => [],
        ];

        /**
         * General tab
         */
        $tab = [
            'id' => 'document',
            'name' => Lang::get('global.settings_general'),
            'class' => 'flex flex-wrap pt-4 px-2',
            'active' => true,
        ];

        // template
        $template = [
            [
                'key' => $document->template,
                'value' => $document->tpl->templatename ?? 'blank',
                'selected' => true,
            ],
        ];

        $slot = [
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'flex flex-wrap grow md:basis-2/3 xl:basis-9/12 px-2 pb-0',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Section',
                            'attrs' => [
                                'label' => 'Содержимое',
                                'expanded' => true,
                            ],
                            'slots' => [
                                'default' => [
                                    [
                                        'component' => 'Fields/Input',
                                        'model' => 'pagetitle',
                                        'attrs' => [
                                            'label' => Lang::get('global.resource_title'),
                                            'help' => '<b>[*pagetitle*]</b><br>' .
                                                Lang::get('global.resource_title_help'),
                                            'required' => true,
                                            'class' => 'md:pr-2 md:basis-2/3',
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Input',
                                        'model' => 'alias',
                                        'attrs' => [
                                            'label' => Lang::get('global.resource_alias'),
                                            'help' => '<b>[*alias*]</b><br>' .
                                                Lang::get('global.resource_alias_help'),
                                            'class' => 'md:pl-2 md:basis-1/3',
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Input',
                                        'model' => 'longtitle',
                                        'attrs' => [
                                            'label' => Lang::get('global.long_title'),
                                            'help' => '<b>[*longtitle*]</b><br>' .
                                                Lang::get('global.resource_long_title_help'),
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Textarea',
                                        'model' => 'description',
                                        'attrs' => [
                                            'label' => Lang::get('global.resource_description'),
                                            'help' => '<b>[*description*]</b><br>' .
                                                Lang::get('global.resource_description_help'),
                                            'class' => 'md:pr-2 md:basis-1/2',
                                            'rows' => 3,
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Textarea',
                                        'model' => 'introtext',
                                        'attrs' => [
                                            'label' => Lang::get('global.resource_summary'),
                                            'help' => '<b>[*introtext*]</b><br>' .
                                                Lang::get('global.resource_summary_help'),
                                            'class' => 'md:pl-2 md:basis-1/2',
                                            'rows' => 3,
                                        ],
                                    ],
                                    $document->richtext
                                        ?
                                        [
                                            'component' => 'CodeEditor',
                                            'model' => 'content',
                                            'attrs' => [
                                                'label' => Lang::get('global.resource_content'),
                                                'help' => '<b>[*content*]</b>',
                                                'rows' => 21,
                                                'config' => [
                                                    [
                                                        'component' => 'Textarea',
                                                        'name' => Lang::get('global.none'),
                                                    ],
                                                    [
                                                        'component' => 'Codemirror',
                                                        'name' => 'Codemirror',
                                                        'active' => true,
                                                        'lang' => 'html',
                                                    ],
                                                ],
                                            ],
                                        ]
                                        :
                                        [
                                            'component' => 'Fields/Textarea',
                                            'model' => 'content',
                                            'attrs' => [
                                                'label' => Lang::get('global.resource_content'),
                                                'help' => '<b>[*content*]</b>',
                                                'rows' => 21,
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
                    'class' => 'flex flex-wrap grow md:basis-1/3 xl:basis-3/12 px-2 pb-0',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Section',
                            'attrs' => [
                                'label' => 'Публикация',
                                'expanded' => true,
                            ],
                            'slots' => [
                                'default' => [
                                    [
                                        'component' => 'Fields/Checkbox',
                                        'model' => 'published',
                                        'attrs' => [
                                            'label' => Lang::get('global.resource_opt_published'),
                                            'help' => '<b>[*published*]</b><br>' .
                                                Lang::get('global.resource_opt_published_help'),
                                            //'value' => 1,
                                            'trueValue' => 1,
                                            'falseValue' => 0,
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Checkbox',
                                        'model' => 'deleted',
                                        'attrs' => [
                                            'label' => Lang::get('global.deleted'),
                                            'help' => '<b>[*deleted*]</b>',
                                            //'value' => 1,
                                            'trueValue' => 1,
                                            'falseValue' => 0,
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Input',
                                        'model' => 'publishedon',
                                        'attrs' => [
                                            'label' => Lang::get('global.page_data_published'),
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Input',
                                        'model' => 'pub_date',
                                        'attrs' => [
                                            'label' => Lang::get('global.page_data_publishdate'),
                                            'help' => '<b>[*pub_date*]</b><br>' .
                                                Lang::get('global.page_data_publishdate_help'),
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Input',
                                        'model' => 'unpub_date',
                                        'attrs' => [
                                            'label' => Lang::get('global.page_data_unpublishdate'),
                                            'help' => '<b>[*unpub_date*]</b><br>' .
                                                Lang::get('global.page_data_unpublishdate_help'),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'component' => 'Section',
                            'attrs' => [
                                'label' => Lang::get('global.page_data_template'),
                                'expanded' => true,
                            ],
                            'slots' => [
                                'default' => [
                                    [
                                        'component' => 'Fields/Select',
                                        'model' => 'template',
                                        'attrs' => [
                                            'label' => Lang::get('global.page_data_template'),
                                            'help' => '<b>[*template*]</b><br>' .
                                                Lang::get('global.page_data_template_help'),
                                            'data' => $template,
                                            'url' => 'api/template/select',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'component' => 'Section',
                            'attrs' => [
                                'label' => 'Поведение в меню',
                                'expanded' => true,
                            ],
                            'slots' => [
                                'default' => [
                                    [
                                        'component' => 'Fields/Checkbox',
                                        'model' => 'hidemenu',
                                        'attrs' => [
                                            'label' => Lang::get('global.resource_opt_show_menu'),
                                            'help' => '<b>[*hidemenu*]</b><br>' .
                                                Lang::get('global.resource_opt_show_menu_help'),
                                            'trueValue' => 0,
                                            'falseValue' => 1,
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Input',
                                        'model' => 'menutitle',
                                        'attrs' => [
                                            'label' => Lang::get('global.resource_opt_menu_title'),
                                            'help' => '<b>[*menutitle*]</b><br>' .
                                                Lang::get('global.resource_opt_menu_title_help'),
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Input',
                                        'model' => 'link_attributes',
                                        'attrs' => [
                                            'label' => Lang::get('global.link_attributes'),
                                            'help' => '<b>[*link_attributes*]</b><br>' .
                                                Lang::get('global.link_attributes_help'),
                                        ],
                                    ],
                                    [
                                        'component' => 'Fields/Input',
                                        'model' => 'menuindex',
                                        'attrs' => [
                                            'label' => Lang::get('global.resource_opt_menu_index'),
                                            'help' => '<b>[*menuindex*]</b><br>' .
                                                Lang::get('global.resource_opt_menu_index_help'),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $tabs['attrs']['data'][] = $tab;
        $tabs['slots']['document'] = $slot;

        /**
         * Settings tab
         */
        $tab = [
            'id' => 'settings',
            'name' => Lang::get('global.settings_page_settings'),
            'class' => 'flex flex-wrap px-4 py-8',
        ];

        // types
        $types = [
            'document' => Lang::get('global.resource_type_webpage'),
            'reference' => Lang::get('global.resource_type_weblink'),
        ];

        array_walk($types, fn(&$v, $k) => $v = [
            'key' => $k,
            'value' => $v,
            'selected' => $document->type == $k,
        ]);

        // contentTypes
        $contentTypes = explode(',', Config::get('global.custom_contenttype'));
        sort($contentTypes);

        $contentTypes = array_map(fn($k) => [
            'key' => $k,
            'value' => $k,
            'selected' => $document->contentType == $k,
        ], $contentTypes);

        // content_dispo
        $content_dispo = [Lang::get('global.inline'), Lang::get('global.attachment')];

        array_walk($content_dispo, fn(&$v, $k) => $v = [
            'key' => $k,
            'value' => $v,
            'selected' => $document->content_dispo == $k,
        ]);

        // parent
        $parent = $document->parent ? $document->parents->pagetitle . ' (' . $document->parent . ')' : 0;

        $slot = [
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'flex flex-wrap grow md:basis-1/2 px-4',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Fields/Select',
                            'model' => 'type',
                            'attrs' => [
                                'label' => Lang::get('global.resource_type'),
                                'help' => '<b>[*type*]</b><br>' .
                                    Lang::get('global.resource_type_message'),
                                'required' => true,
                                'data' => $types,
                            ],
                        ],
                        [
                            'component' => 'Fields/Select',
                            'model' => 'contentType',
                            'attrs' => [
                                'label' => Lang::get('global.page_data_contentType'),
                                'help' => '<b>[*contentType*]</b><br>' .
                                    Lang::get('global.page_data_contentType_help'),
                                'required' => true,
                                'data' => $contentTypes,
                            ],
                        ],
                        [
                            'component' => 'Fields/Checkbox',
                            'model' => 'isfolder',
                            'attrs' => [
                                'label' => Lang::get('global.resource_opt_folder'),
                                'help' => '<b>[*isfolder*]</b><br>' .
                                    Lang::get('global.resource_opt_folder_help'),
                                'trueValue' => 1,
                                'falseValue' => 0,
                            ],
                        ],
                        [
                            'component' => 'Fields/Checkbox',
                            'model' => 'hide_from_tree',
                            'attrs' => [
                                'label' => Lang::get('global.track_visitors_title'),
                                'help' => '<b>[*hide_from_tree*]</b><br>' .
                                    Lang::get('global.resource_opt_trackvisit_help'),
                                //'value' => 0,
                                'trueValue' => 0,
                                'falseValue' => 1,
                            ],
                        ],
                        [
                            'component' => 'Fields/Checkbox',
                            'model' => 'alias_visible',
                            'attrs' => [
                                'label' => Lang::get('global.resource_opt_alvisibled'),
                                'help' => '<b>[*alias_visible*]</b><br>' .
                                    Lang::get('global.resource_opt_alvisibled_help'),
                                //'value' => 1,
                                'trueValue' => 1,
                                'falseValue' => 0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'component' => 'Template',
                'attrs' => [
                    'class' => 'flex flex-wrap grow md:basis-1/2 px-4',
                ],
                'slots' => [
                    'default' => [
                        [
                            'component' => 'Fields/Input',
                            'model' => 'parent',
                            'attrs' => [
                                'label' => Lang::get('global.import_parent_resource'),
                                'help' => '<b>[*parent*]</b><br>' .
                                    Lang::get('global.resource_parent_help'),
                                'required' => true,
                                'value' => $parent,
                                'inputClass' => 'pr-8 appearance-select cursor-pointer',
                                'inputReadonly' => true,
                            ],
                        ],
                        [
                            'component' => 'Fields/Select',
                            'model' => 'content_dispo',
                            'attrs' => [
                                'label' => Lang::get('global.resource_opt_contentdispo'),
                                'help' => '<b>[*content_dispo*]</b><br>' .
                                    Lang::get('global.resource_opt_contentdispo_help'),
                                'data' => $content_dispo,
                            ],
                        ],
                        [
                            'component' => 'Fields/Checkbox',
                            'model' => 'richtext',
                            'attrs' => [
                                'label' => Lang::get('global.resource_opt_richtext'),
                                'help' => '<b>[*richtext*]</b><br>' .
                                    Lang::get('global.resource_opt_richtext_help'),
                                //'value' => 1,
                                'trueValue' => 1,
                                'falseValue' => 0,
                            ],
                        ],
                        [
                            'component' => 'Fields/Checkbox',
                            'model' => 'searchable',
                            'attrs' => [
                                'label' => Lang::get('global.page_data_searchable'),
                                'help' => '<b>[*searchable*]</b><br>' .
                                    Lang::get('global.page_data_searchable_help'),
                                //'value' => 1,
                                'trueValue' => 1,
                                'falseValue' => 0,
                            ],
                        ],
                        [
                            'component' => 'Fields/Checkbox',
                            'model' => 'cacheable',
                            'attrs' => [
                                'label' => Lang::get('global.page_data_cacheable'),
                                'help' => '<b>[*cacheable*]</b><br>' .
                                    Lang::get('global.page_data_cacheable_help'),
                                //'value' => 1,
                                'trueValue' => 1,
                                'falseValue' => 0,
                            ],
                        ],
                        [
                            'component' => 'Fields/Checkbox',
                            'model' => 'empty_cache',
                            'attrs' => [
                                'label' => Lang::get('global.resource_opt_emptycache'),
                                'help' => Lang::get('global.resource_opt_emptycache_help'),
                                'value' => 1,
                                'trueValue' => 1,
                                'falseValue' => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $tabs['attrs']['data'][] = $tab;
        $tabs['slots']['settings'] = $slot;

        /**
         * Tvs tab
         */
        $tvs = $document->getTvs();

        if ($tvs->count()) {
            $tab = [
                'id' => 'tvs',
                'name' => Lang::get('global.settings_templvars'),
                'class' => 'flex flex-wrap',
            ];

            $slot = [
                'component' => 'Tabs',
                'attrs' => [
                    'id' => 'tvs',
                    'class' => 'h-full',
                    'data' => [],
                    'vertical' => true,
                ],
                'slots' => [],
            ];

            $components = [
                'text' => 'Fields/Input',
                'number' => 'Fields/Input',
                'dropdown' => 'Fields/Select',
                'checkbox' => 'Fields/Checkbox',
                'option' => 'Fields/Radio',
                'textarea' => 'Fields/Textarea',
                'textareamini' => 'Fields/Textarea',
                'richtext' => 'Fields/Textarea',
                'file' => 'Fields/File',
                'image' => 'Fields/File',
            ];

            $componentsAttrs = [
                'email' => [
                    'type' => 'email',
                ],
                'tel' => [
                    'type' => 'tel',
                ],
                'number' => [
                    'type' => 'number',
                ],
                'date' => [
                    'type' => 'datetime-local',
                ],
                'textarea' => [
                    'rows' => 10,
                ],
                'file' => [
                    'type' => 'file',
                ],
                'image' => [
                    'type' => 'image',
                ],
            ];

            $activeFirst = true;

            foreach ($tvs as $tv) {
                $categoryId = 'category-' . $tv['category'];

                if (!isset($slot['attrs']['data'][$categoryId])) {
                    $slot['attrs']['data'][$categoryId] = [
                        'id' => $categoryId,
                        'name' => $tv['category_name'],
                        'class' => 'flex flex-wrap h-full p-8',
                        'active' => $activeFirst,
                    ];

                    $activeFirst = false;
                }

                $custom = str_starts_with($tv['type'], 'custom_tv:');

                $tvSlot = [
                    'component' => $components[$tv['type']] ?? $components['text'],
                    'model' => $tv['name'],
                ];

                $tvSlot['attrs'] = array_merge([
                    'label' => $tv['caption'],
                    'description' => $tv['description'],
                    'help' => '<b>[*' . $tv['name'] . '*]</b><i class="badge">' . $tv['id'] . '</i><br>' .
                        $tv['description'],
                ], $componentsAttrs[$tv['type']] ?? []);

                $tvSlot['attrs']['data'] = array_map(function ($item) {
                    if (stripos($item, '==')) {
                        [$value, $key] = explode('==', $item);
                    } else {
                        $value = $key = $item;
                    }

                    return [
                        'key' => $key,
                        'value' => $value,
                    ];
                }, explode('||', $tv['elements']));

                if ($custom) {
                    // custom tv
                    $tvSlot = [];
                } else {
                    switch ($tv['type']) {
                        case 'radio':
                        case 'checkbox':
                        case 'option':
                        case 'listbox-multiple':
                            if (count($tvSlot['attrs']['data'])) {
                                $tvSlot['attrs']['value'] = $tv['value'] == '' ? [] : explode('||', $tv['value']);
                            } else {
                                $tvSlot['attrs']['value'] = $tv['value'];
                            }

                            break;

                        default:
                    }
                }

                $slot['slots'][$categoryId][] = $tvSlot;
            }

            $slot['attrs']['data'] = array_values($slot['attrs']['data']);

            $tabs['attrs']['data'][] = $tab;
            $tabs['slots']['tvs'] = $slot;
        }

        return [
            $actions,
            $title,
            $tabs,
        ];
    }
}
