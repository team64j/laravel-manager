<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

use Illuminate\Support\Facades\Lang;

class UsersManagementLayout
{
    /**
     * @return array
     */
    public function default(): array
    {
        $yes = '<span class="text-rose-600">' . Lang::get('global.yes') . '</span>';
        $no = '<span class="text-green-600">' . Lang::get('global.no') . '</span>';

        $title = [
            'component' => 'Title',
        ];

        $tabs = [
            'component' => 'Tabs',
            'attrs' => [
                'history' => 'element',
                'id' => 'userManagement',
                'data' => [],
            ],
            'slots' => [],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'roles',
            'name' => Lang::get('global.role_role_management'),
            'icon' => 'fa fa-legal',
            'class' => 'py-4',
        ];

        $tabs['slots']['roles'] = [
            'component' => 'UsersManagement/Roles',
            'data' => 'data',
            'attrs' => [
                'id' => 'roles',
                'route' => 'UsersRole',
                'columns' => [
                    [
                        'name' => 'id',
                        'label' => Lang::get('global.id'),
                        'width' => '5rem',
                        'style' => [
                            'textAlign' => 'right',
                            'fontWeight' => 'bold',
                        ],
                    ],
                    [
                        'name' => 'name',
                        'label' => Lang::get('global.role'),
                        'style' => [
                            'fontWeight' => '500',
                        ],
                    ],
                    [
                        'name' => 'description',
                        'label' => Lang::get('global.description'),
                    ],
                ],
            ],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'categories',
            'name' => Lang::get('global.category_heading'),
            'icon' => 'fa fa-object-group',
            'class' => 'py-4',
        ];

        $tabs['slots']['categories'] = [
            'component' => 'UsersManagement/Categories',
            'data' => 'data',
            'attrs' => [
                'id' => 'categories',
                'route' => 'UsersCategory',
                'columns' => [
                    [
                        'name' => 'id',
                        'label' => Lang::get('global.id'),
                        'width' => '5rem',
                        'style' => [
                            'textAlign' => 'right',
                            'fontWeight' => 'bold',
                        ],
                    ],
                    [
                        'name' => 'name',
                        'label' => Lang::get('global.category_heading'),
                        'style' => [
                            'fontWeight' => '500',
                        ],
                    ],
                ],
            ],
        ];

        $tabs['attrs']['data'][] = [
            'id' => 'permissions',
            'name' => Lang::get('global.manage_permission'),
            'icon' => 'fa fa-user-tag',
            'class' => 'py-4',
        ];

        $tabs['slots']['permissions'] = [
            'component' => 'UsersManagement/Permissions',
            'data' => 'data',
            'attrs' => [
                'id' => 'permissions',
                'route' => 'UsersPermission',
                'filter' => true,
                'columns' => [
                    [
                        'name' => 'id',
                        'label' => Lang::get('global.id'),
                        'width' => '5rem',
                        'style' => [
                            'textAlign' => 'right',
                            'fontWeight' => 'bold',
                        ],
                    ],
                    [
                        'name' => 'name',
                        'label' => Lang::get('global.role_name'),
                        'style' => [
                            'fontWeight' => '500',
                        ],
                    ],
                    [
                        'name' => 'key',
                        'label' => Lang::get('global.key_desc'),
                        'width' => '5rem',
                    ],
                    [
                        'name' => 'disabled',
                        'label' => Lang::get('global.disabled'),
                        'width' => '7rem',
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

        return [
            $title,
            $tabs,
        ];
    }
}
