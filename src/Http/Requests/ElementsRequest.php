<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ElementsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        switch ($this->route()->parameter('element')) {
            case 'templates':
                Auth::user()->hasPermissionsOrFail(
                    ['edit_template', 'new_template']
                );
                break;

            case 'tvs':
                Auth::user()->hasPermissionsOrFail(
                    ['edit_template', 'edit_snippet', 'edit_chunk', 'edit_plugin']
                );
                break;

            case 'chunks':
                Auth::user()->hasPermissionsOrFail(
                    ['edit_chunk', 'new_chunk']
                );
                break;

            case 'snippets':
                Auth::user()->hasPermissionsOrFail(
                    ['edit_snippet', 'new_snippet']
                );
                break;

            case 'plugins':
                Auth::user()->hasPermissionsOrFail(
                    ['edit_plugin', 'new_plugin']
                );
                break;

            case 'modules':
                Auth::user()->hasPermissionsOrFail(
                    ['edit_module', 'new_module']
                );
                break;

            case 'categories':
                Auth::user()->hasPermissionsOrFail(
                    ['category_manager']
                );
                break;

            default:
                return true;
        }

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
}
