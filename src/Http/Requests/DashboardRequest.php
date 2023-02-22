<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Team64j\LaravelManager\Http\Controllers\DashboardController;

class DashboardRequest extends FormRequest
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
     * @return array[]
     */
    public static function getRoutes(): array
    {
        return [
            [
                'method' => 'get',
                'uri' => 'sidebar',
                'action' => [DashboardController::class, 'getSidebar'],
            ],
            [
                'method' => 'get',
                'uri' => 'news',
                'action' => [DashboardController::class, 'getNews'],
            ],
            [
                'method' => 'get',
                'uri' => 'news-security',
                'action' => [DashboardController::class, 'getNewsSecurity'],
            ],
        ];
    }
}
