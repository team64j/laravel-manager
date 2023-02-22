<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Team64j\LaravelManager\Http\Controllers\TvController;

class TvRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getRoutes(): array
    {
        return [
            [
                'method' => 'get',
                'uri' => 'sort',
                'action' => [TvController::class, 'sort'],
            ],
            [
                'method' => 'get',
                'uri' => 'types',
                'action' => [TvController::class, 'types'],
            ],
            [
                'method' => 'get',
                'uri' => 'categories',
                'action' => [TvController::class, 'categories'],
            ],
            [
                'method' => 'get',
                'uri' => 'tree',
                'action' => [TvController::class, 'tree'],
            ],
        ];
    }
}
