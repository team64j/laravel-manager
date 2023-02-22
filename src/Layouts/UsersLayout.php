<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

use Illuminate\Support\Facades\Lang;

class UsersLayout
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
                'id' => 'users',
                'class' => 'grow py-4',
                'route' => 'User',
            ],
        ];

        return [
            $title,
            $panel,
        ];
    }
}
