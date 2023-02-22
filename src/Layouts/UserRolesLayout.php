<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

class UserRolesLayout
{
    /**
     * @return array
     */
    public function default(): array
    {
        $title = [
            'component' => 'Title',
        ];

        $panel = [
            'component' => 'Panel',
            'data' => 'data',
            'attrs' => [
                'id' => 'roles',
                'class' => 'grow py-4',
                'route' => 'Role',
            ],
        ];

        return [
            $title,
            $panel,
        ];
    }
}
