<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Team64j\LaravelManager\Http\Requests\FilesRequest;
use Team64j\LaravelManager\Http\Resources\FilesResource;

class FilesController
{
    /**
     * @param FilesRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(FilesRequest $request): AnonymousResourceCollection
    {
        return $this->show($request, '');
    }

    /**
     * @param FilesRequest $request
     * @param string $files
     *
     * @return AnonymousResourceCollection
     */
    public function show(FilesRequest $request, string $files): AnonymousResourceCollection
    {
        $data = [];
        $root = realpath(Config::get('global.rb_base_dir', App::basePath('../')));
        $parent = trim(base64_decode($files), './');
        $parentPath = $root . ($parent ? DIRECTORY_SEPARATOR . $parent : '');
        $opened = $request->has('opened') ? $request->string('opened')
            ->explode(',')
            ->map(fn($i) => $i)
            ->toArray() : [];

        $directories = File::directories($parentPath);
        $files = File::files($parentPath, true);

        foreach ($directories as $directory) {
            $title = basename($directory);
            $key = base64_encode(
                trim(
                    str_replace(
                        $root,
                        '',
                        $directory
                    ),
                    DIRECTORY_SEPARATOR
                )
            );

            $item = [
                'key' => $key,
                'title' => $title,
                'folder' => true,
                'size' => $this->getSize(File::size($directory)),
                'date' => $this->getDate(filemtime($directory)),
                'icon.html' => '<i class="far fa-folder fa-fw"></i>'
            ];

            if (in_array($key, $opened)) {
                $newRequest = clone $request;
                $newRequest->query->set('after', null);
                $newRequest->query->set('parent', $key);
                $item['data'] = $this->show($newRequest, '')['data'] ?? [];
            }

            $data[] = $item;
        }

        foreach ($files as $file) {
            $title = $file->getFilename();
            $key = base64_encode(
                str_replace(
                    DIRECTORY_SEPARATOR,
                    '/',
                    trim(str_replace($root, '', $file->getPathname()), DIRECTORY_SEPARATOR)
                )
            );

            $type = $file->getExtension();

            if (!$type) {
                $mimeType = File::mimeType($file->getPathname());

                $type = match ($mimeType) {
                    'text/plain' => 'txt',
                    default => ''
                };
            }

            $item = [
                'key' => $key,
                'title' => $title,
                'folder' => false,
                'type' => $type,
                'unpublished' => !$file->isWritable() || !$file->isReadable(),
                'class' => 'f-ext-' . $file->getExtension(),
                'size' => $this->getSize($file->getSize()),
                'date' => $this->getDate($file->getATime()),
                'icon.html' => '<i class="far fa-file fa-fw"></i>',
            ];

            $mimeType = File::mimeType($file->getPathname());
            $isImage = str_starts_with($mimeType, 'image/') || $file->getExtension() == 'svg';

            if ($isImage) {
                $folderBase = str_replace(
                    realpath(App::basePath('../')),
                    '',
                    realpath(Config::get('global.rb_base_dir'))
                );

                $imageUrl = str_replace(
                    DIRECTORY_SEPARATOR,
                    '/',
                    $folderBase . str_replace($root, '', $file->getPathname())
                );

                $item['icon.html'] = '<img src="' . URL::to($imageUrl) . '" />';
            }

            $data[] = $item;
        }

        return FilesResource::collection([
            'data' => [
                'data' => $data,
                'category' => true,
                'columns' => [
                    [
                        'name' => 'icon',
                        'label' => Lang::get('global.icon'),
                        'width' => '2rem',
                        'style' => [
                            'textAlign' => 'center',
                        ],
                    ],
                    [
                        'name' => 'title',
                        'label' => Lang::get('global.files_filename'),
                    ],
                    [
                        'name' => 'size',
                        'label' => Lang::get('global.files_filesize'),
                        'width' => '12rem',
                        'style' => [
                            'textAlign' => 'right',
                        ],
                    ],
                    [
                        'name' => 'date',
                        'label' => Lang::get('global.datechanged'),
                        'width' => '12rem',
                        'style' => [
                            'textAlign' => 'right',
                            'whiteSpace' => 'nowrap',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param FilesRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function tree(FilesRequest $request): AnonymousResourceCollection
    {
        $data = [];
        $root = realpath(Config::get('global.rb_base_dir', App::basePath('../')));
        $parent = trim(base64_decode($request->input('parent', '')), './');
        $parentPath = $root . DIRECTORY_SEPARATOR . $parent;
        $opened = $request->has('opened') ? $request->string('opened')
            ->explode(',')
            ->map(fn($i) => $i)
            ->toArray() : [];

        $directories = File::directories($parentPath);

        foreach ($directories as $directory) {
            $title = basename($directory);
            $key = base64_encode(
                trim(
                    str_replace(
                        $root,
                        '',
                        $directory
                    ),
                    DIRECTORY_SEPARATOR
                )
            );

            $item = [
                'key' => $key,
                'title' => $title,
                'folder' => true,
                'category' => true,
            ];

            if (in_array($key, $opened)) {
                $newRequest = clone $request;
                $newRequest->query->set('after', null);
                $newRequest->query->set('parent', $key);
                $item['data'] = $this->tree($newRequest)['data'] ?? [];
            }

            $item['hideChildren'] = !File::directories($directory);

            $data[] = $item;
        }

        return FilesResource::collection([
            'data' => [
                'data' => $data,
            ],
        ]);
    }

    /**
     * @param int $size
     *
     * @return string
     */
    protected function getSize(int $size = 0): string
    {
        $mb = 1000 * 1024;

        if ($size > $mb) {
            return round($size / $mb, 2) . ' MB';
        }

        return round($size / 1000, 2) . ' KB';
    }

    /**
     * @param int $date
     *
     * @return string
     */
    protected function getDate(int $date): string
    {
        return Carbon::createFromFormat('U', (string) $date)->format('d-m-Y H:i:s');
    }
}
