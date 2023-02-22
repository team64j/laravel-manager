<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Team64j\LaravelManager\Http\Controllers\ModuleController;

class ModuleRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        switch ($this->route()->getActionMethod()) {
            case 'exec':
            case 'execList':
                Auth::user()->hasPermissionsOrFail(['exec_module']);
                break;

            case 'index':
            case 'show':
                Auth::user()->hasPermissionsOrFail(['edit_module']);
                break;

            case 'store':
                Auth::user()->hasPermissionsOrFail(['new_module']);
                break;

            case 'update':
                Auth::user()->hasPermissionsOrFail(['save_module']);
                break;

            case 'destroy':
                Auth::user()->hasPermissionsOrFail(['delete_module']);
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
     * @return array
     */
    public static function getRoutes(): array
    {
        return [
            [
                'method' => 'get',
                'uri' => 'exec',
                'action' => [ModuleController::class, 'exec'],
            ],
            [
                'method' => 'get',
                'uri' => 'categories',
                'action' => [ModuleController::class, 'categories'],
            ],
            [
                'method' => 'get',
                'uri' => 'tree',
                'action' => [ModuleController::class, 'tree'],
            ],
        ];
    }
}
