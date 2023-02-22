<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Team64j\LaravelManager\Http\Controllers\TemplateController;

class TemplateRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        switch ($this->route()->getActionMethod()) {
            case 'index':
            case 'show':
                Auth::user()->hasPermissionsOrFail(['edit_template']);
                break;

            case 'store':
                Auth::user()->hasPermissionsOrFail(['new_template']);
                break;

            case 'update':
                Auth::user()->hasPermissionsOrFail(['save_template']);
                break;

            case 'destroy':
                Auth::user()->hasPermissionsOrFail(['delete_template']);
                break;
        }

        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'templatename' => ['string'],
            'templatealias' => ['string', 'nullable'],
            'description' => ['string'],
            'editor_type' => ['int'],
            'category' => ['int'],
            'icon' => ['string'],
            'template_type' => ['int'],
            'content' => ['string', 'nullable'],
            'locked' => ['int'],
            'selectable' => ['int'],
        ];
    }

    public function attributes(): array
    {
        return [
            'templatename' => '"' . __('global.template_name') . '"',
            'templatealias' => '"' . __('global.alias') . '"',
            'description' => '"' . __('global.template_desc') . '"',
            'icon' => '"' . __('global.alias') . '"',
            'content' => '"' . __('global.template_code') . '"',
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
                'uri' => 'select',
                'action' => [TemplateController::class, 'select'],
            ],
            [
                'method' => 'get',
                'uri' => 'categories',
                'action' => [TemplateController::class, 'categories'],
            ],
            [
                'method' => 'get',
                'uri' => 'tree',
                'action' => [TemplateController::class, 'tree'],
            ],
        ];
    }
}
