<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Layouts;

use Illuminate\Support\Facades\Lang;

class FilesLayout
{
    /**
     * @return array
     */
    public function default(): array
    {
        $title = [
            'component' => 'Title',
            'attrs' => [
                'data' => Lang::get('global.files_management'),
                'id' => null,
                'icon' => 'far fa-folder-open'
            ]
        ];

        return [
            $title,
        ];
    }
}
