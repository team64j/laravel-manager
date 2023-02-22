<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Team64j\LaravelManager\Http\Controllers\SnippetController;

class SnippetRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        switch ($this->route()->getActionMethod()) {
            case 'index':
            case 'show':
                Auth::user()->hasPermissionsOrFail(['edit_snippet']);
                break;

            case 'store':
                Auth::user()->hasPermissionsOrFail(['new_snippet']);
                break;

            case 'update':
                Auth::user()->hasPermissionsOrFail(['save_snippet']);
                break;

            case 'destroy':
                Auth::user()->hasPermissionsOrFail(['delete_snippet']);
                break;
        }

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
                'uri' => 'categories',
                'action' => [SnippetController::class, 'categories'],
            ],
            [
                'method' => 'get',
                'uri' => 'tree',
                'action' => [SnippetController::class, 'tree'],
            ],
        ];
    }
}
