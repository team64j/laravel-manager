<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Team64j\LaravelManager\Http\Controllers\PluginController;

class PluginRequest extends FormRequest
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
                'action' => [PluginController::class, 'sort'],
            ],
            [
                'method' => 'get',
                'uri' => 'categories',
                'action' => [PluginController::class, 'categories'],
            ],
            [
                'method' => 'get',
                'uri' => 'tree',
                'action' => [PluginController::class, 'tree'],
            ],
        ];
    }
}
