<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

class EventLogLayout
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
                'route' => 'EventLog',
            ],
        ];

        return [
            $actions,
            $title,
            $panel,
        ];
    }
}
