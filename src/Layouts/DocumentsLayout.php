<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

use Illuminate\Support\Facades\Lang;

class DocumentsLayout
{
    /**
     * @return array
     */
    public function default(): array
    {
        $title = [
            'component' => 'Title',
            'data' => 'meta.pagetitle',
        ];

        $panel = [
            'component' => 'Panel',
            'data' => 'data',
            'attrs' => [
                'class' => 'py-4',
                'route' => 'Document',
                'columns' => [
                    [
                        'name' => 'id',
                        'label' => Lang::get('global.id'),
                        'sort' => true,
                        'width' => '4rem',
                        'style' => [
                            'textAlign' => 'right',
                        ],
                    ],
                    [
                        'name' => 'isfolder',
                        'label' => Lang::get('global.folder'),
                        'sort' => true,
                        'width' => '4rem',
                        'values' => [
                            0 => '<i class="far fa-file"></i>',
                            1 => '<i class="fa fa-folder"</i>',
                        ],
                    ],
                    [
                        'name' => 'pagetitle',
                        'label' => Lang::get('global.pagetitle'),
                        'sort' => true,
                    ],
                    [
                        'name' => 'createdon',
                        'label' => Lang::get('global.createdon'),
                        'sort' => true,
                        'width' => '12rem',
                        'style' => [
                            'textAlign' => 'center',
                        ],
                    ],
                    [
                        'name' => 'publishedon',
                        'label' => Lang::get('global.publish_date'),
                        'sort' => true,
                        'width' => '12rem',
                        'style' => [
                            'textAlign' => 'center',
                        ],
                    ],
                    [
                        'name' => 'published',
                        'label' => Lang::get('global.page_data_status'),
                        'sort' => true,
                        'width' => '12rem',
                        'style' => [
                            'textAlign' => 'right',
                        ],
                        'values' => [
                            0 => '<span class="text-rose-600">' . Lang::get('global.page_data_unpublished') . '</span>',
                            1 => '<span class="text-green-600">' . Lang::get('global.page_data_published') . '</span>',
                        ],
                    ],
                ],
            ],
        ];

        return [
            $title,
            $panel,
        ];
    }
}
