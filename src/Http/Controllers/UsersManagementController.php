<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Team64j\LaravelEvolution\Models\DocumentgroupName;
use Team64j\LaravelEvolution\Models\MembergroupName;
use Illuminate\Support\Facades\App;
use Team64j\LaravelManager\Http\Requests\UsersManagementRequest;
use Team64j\LaravelManager\Http\Resources\UsersManagementResource;
use Team64j\LaravelManager\Layouts\UsersManagementLayout;

class UsersManagementController extends Controller
{
    /**
     * @param UsersManagementRequest $request
     * @param string $element
     * @param UsersManagementLayout $layout
     *
     * @return UsersManagementResource
     */
    public function show(
        UsersManagementRequest $request,
        string $element,
        UsersManagementLayout $layout): UsersManagementResource
    {
        $data = match ($element) {
            'roles' => App::call('\Manager\Http\Controllers\UserRoleController@index', [])->collection,
            'categories' => App::call('\Manager\Http\Controllers\UserCategoryController@index', [])->collection,
            'permissions' => App::call('\Manager\Http\Controllers\UserPermissionController@index', [])->collection,
            default => []
        };

        return new UsersManagementResource([
            'data' => $data,
            'meta' => [],
            'layout' => $layout->default(),
        ]);
    }

    /**
     * @param UsersManagementRequest $request
     *
     * @return UsersManagementResource
     */
    public function getGroupUsers(UsersManagementRequest $request): UsersManagementResource
    {
        return new UsersManagementResource(
            MembergroupName::query()
                ->with('users')
                ->orderBy('name')
                ->get()
        );
    }

    /**
     * @param UsersManagementRequest $request
     *
     * @return UsersManagementResource
     */
    public function getGroupDocuments(UsersManagementRequest $request): UsersManagementResource
    {
        return new UsersManagementResource(
            DocumentgroupName::query()
                ->with('documents')
                ->orderBy('name')
                ->get()
        );
    }

    /**
     * @param UsersManagementRequest $request
     *
     * @return UsersManagementResource
     */
    public function getGroupRelations(UsersManagementRequest $request): UsersManagementResource
    {
        return new UsersManagementResource([
            'data' => [
                'groupUsers' => MembergroupName::query()
                    ->with('documentGroups')
                    ->orderBy('name')
                    ->get(),
                'groupDocuments' => DocumentgroupName::query()
                    ->with('documents')
                    ->orderBy('name')
                    ->get(),
            ],
        ]);
    }
}
