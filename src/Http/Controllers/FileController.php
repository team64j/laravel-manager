<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Team64j\LaravelManager\Http\Requests\FileRequest;
use Team64j\LaravelManager\Http\Resources\FileResource;

class FileController
{
    /**
     * @param FileRequest $request
     * @param string $file
     *
     * @return FileResource
     */
    public function index(FileRequest $request, string $file): FileResource
    {
        $data = [];
        $root = realpath(Config::get('global.filemanager_path', App::basePath('../')));
        $filename = trim(base64_decode(urldecode($file)), '/');
        $path = $root . DIRECTORY_SEPARATOR . $filename;
        $types = [
            'text/plain',
            'image/svg+xml',
            'application/json',
            'application/octet-stream',
        ];

        $ignoreExtensions = [
            'woff',
            'woff2',
        ];

        if (File::isFile($path)) {
            $data['path'] = $filename;
            $data['name'] = File::name($path);
            $data['basename'] = File::basename($path);
            $data['type'] = File::mimeType($path);
            $data['ext'] = File::extension($path);
            $data['lang'] = '';

            $content = File::get($path);

            if (str_starts_with($content, '#!/usr/bin/env php')) {
                $data['lang'] = 'php';
            }

            if (!in_array($data['ext'], $ignoreExtensions, true)) {
                if (in_array($data['type'], $types) || Str::startsWith($data['type'], 'text/')) {
                    $data['content'] = $content;
                }
            }
        }

        return new FileResource($data);
    }

    /**
     * @param FileRequest $request
     * @param string $file
     *
     * @return FileResource
     */
    public function show(FileRequest $request, string $file): FileResource
    {
        return $this->index($request, $file);
    }

    /**
     * @param FileRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function tree(FileRequest $request): AnonymousResourceCollection
    {
        $data = [];
        $root = realpath(Config::get('global.filemanager_path', App::basePath('../')));
        $parent = trim(base64_decode($request->input('parent', '')), './');
        $parentPath = $root . DIRECTORY_SEPARATOR . $parent;
        $after = basename(base64_decode($request->string('after', '')->toString()));
        $opened = $request->has('opened') ? $request->string('opened')
            ->explode(',')
            ->map(fn($i) => $i)
            ->toArray() : [];

        $directories = File::directories($parentPath);
        $files = File::files($parentPath, true);
        $checkAfter = true;
        $limit = Config::get('global.number_of_results');
        $counter = -1;
        $next = '';

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

            if ($checkAfter && $after) {
                if ($after == $title) {
                    $after = null;
                    $checkAfter = false;
                }

                continue;
            }

            if ($counter++ > $limit) {
                break;
            }

            $item = [
                'key' => $key,
                'title' => $title,
                'folder' => true,
                'date' => $this->getDate(filemtime($directory)),
            ];

            if (in_array($key, $opened)) {
                $newRequest = clone $request;
                $newRequest->query->set('after', null);
                $newRequest->query->set('parent', $key);
                $item['data'] = $this->tree($newRequest)['data'] ?? [];
            }

            $data[] = $item;

            $next = 'api/file/tree?parent=' . base64_encode($parent) . '&after=' . base64_encode($title);
        }

        $checkAfter = true;

        foreach ($files as $file) {
            $title = $file->getFilename();
            $key = base64_encode(trim(str_replace($root, '', $file->getPathname()), DIRECTORY_SEPARATOR));

            if ($checkAfter && $after) {
                if ($after == $title) {
                    $after = null;
                    $checkAfter = false;
                }

                continue;
            }

            if ($counter++ > $limit) {
                break;
            }

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
                'type' => $type,
                'unpublished' => !$file->isWritable() || !$file->isReadable(),
                'class' => 'f-ext-' . $file->getExtension(),
                'size' => $this->getSize($file->getSize()),
                'date' => $this->getDate($file->getATime()),
            ];

            $data[] = $item;

            $next = 'api/file/tree?parent=' . base64_encode($parent) . '&after=' . base64_encode($title);
        }

        if (count($data) <= $limit) {
            $next = null;
        }

        return FileResource::collection([
            'data' => [
                'data' => $data,
                'category' => true,
                'pagination' => [
                    'next' => $next,
                ],
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
