<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Team64j\LaravelManager\Http\Controllers\UsersManagementController;

class UsersManagementRequest extends FormRequest
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
     * @return array[]
     */
    public static function getRoutes(): array
    {
        return [
            [
                'method' => 'get',
                'uri' => 'users',
                'action' => [UsersManagementController::class, 'getGroupUsers'],
            ],
            [
                'method' => 'get',
                'uri' => 'documents',
                'action' => [UsersManagementController::class, 'getGroupDocuments'],
            ],
            [
                'method' => 'get',
                'uri' => 'relations',
                'action' => [UsersManagementController::class, 'getGroupRelations'],
            ],
        ];
    }
}
