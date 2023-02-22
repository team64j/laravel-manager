<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Team64j\LaravelManager\Http\Controllers\CategoryController;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            //
        ];
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
                'action' => [CategoryController::class, 'sort'],
            ],
            [
                'method' => 'get',
                'uri' => 'select',
                'action' => [CategoryController::class, 'select'],
            ],
            [
                'method' => 'get',
                'uri' => 'categories',
                'action' => [CategoryController::class, 'categories'],
            ],
            [
                'method' => 'get',
                'uri' => 'tree',
                'action' => [CategoryController::class, 'tree'],
            ],
        ];
    }
}
