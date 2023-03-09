<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

class SystemLogLayout
{
    /**
     * @return array
     */
    public function default(): array
    {
        $actions = [
            'component' => 'ActionsButtons',
            'attrs' => [
                'data' => ['clear'],
            ],
        ];

        $title = [
            'component' => 'Title',
        ];

        $panel = [
            'component' => 'Panel',
            'data' => 'data',
            'attrs' => [
                'class' => 'py-4',
                'history' => true,
            ],
        ];

        return [
            $actions,
            $title,
            $panel,
        ];
    }
}
