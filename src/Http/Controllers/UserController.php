<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Team64j\LaravelEvolution\Models\ActiveUserSession;
use Team64j\LaravelEvolution\Models\User;
use Team64j\LaravelEvolution\Models\UserAttribute;
use Team64j\LaravelEvolution\Models\UserRole;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\UserRequest;
use Team64j\LaravelManager\Http\Resources\UserResource;
use Team64j\LaravelManager\Layouts\UsersLayout;
use Team64j\LaravelManager\Traits\PaginationTrait;

class UserController extends Controller
{
    use PaginationTrait;

    /**
     * @param UserRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(UserRequest $request): AnonymousResourceCollection
    {
        $filter = $request->get('filter');

        $result = User::query()
            ->when(
                $filter,
                fn($q) => $q->where('username', 'like', '%' . $filter . '%')->orWhere('username', $filter)
            )
            ->paginate(Config::get('global.number_of_results'), ['id', 'username as name']);

        return UserResource::collection([
            'data' => [
                'data' => $result->items(),
                'pagination' => $this->pagination($result),
            ],
        ]);
    }

    /**
     * @param UserRequest $request
     * @param UsersLayout $layout
     *
     * @return AnonymousResourceCollection
     */
    public function list(UserRequest $request, UsersLayout $layout): AnonymousResourceCollection
    {
        $filter = $request->get('filter');
        $order = $request->input('order', 'id');
        $dir = $request->input('dir', 'asc');
        $filterRole = $request->input('role');
        $filterBlocked = $request->input('blocked', '');

        /** @var Collection $filterDatetime */
        $filterDatetime = $request->collect('lastlogin')->map(function ($item, $index) {
            if ($index) {
                $item .= ' 23:59:59';
            } else {
                $item .= ' 00:00:00';
            }

            return strtotime($item);
        });

        $orderFields = ['id', 'name', 'fullname', 'email', 'rolename', 'lastlogin', 'logincount', 'blocked'];

        $yes = '<span class="text-rose-600">' . Lang::get('global.yes') . '</span>';
        $no = '<span class="text-green-600">' . Lang::get('global.no') . '</span>';

        if (!in_array($order, $orderFields)) {
            $order = 'id';
        }

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        $query = UserAttribute::query()
            ->from('users as u')
            ->select([
                'u.id',
                'u.username as name',
                'a.fullname',
                'a.email',
                'a.lastlogin',
                'a.logincount',
                'a.blocked',
                'r.name as rolename',
            ])
            ->join((new UserAttribute())->getTable() . ' as a', 'a.internalKey', 'u.id')
            ->join((new UserRole())->getTable() . ' as r', 'r.id', 'a.role')
            ->when(
                $filter,
                fn($q) => $q->where('u.username', 'like', '%' . $filter . '%')->orWhere('u.username', $filter)
            )
            ->when($filterRole, fn($query) => $query->where('a.role', $filterRole))
            ->when($filterBlocked, fn($query) => $query->where('a.blocked', $filterBlocked));

        $result = $query->clone()
            ->when(in_array($order, $orderFields), fn($q) => $q->orderBy($order, $dir))
            ->when($filterDatetime->count(), fn($query) => $query->whereBetween('a.lastlogin', $filterDatetime))
            ->paginate(Config::get('global.number_of_results'))
            ->appends($request->all());

        $datetimeMin = $query->clone()->where('a.lastlogin', '!=', '')->min('a.lastlogin');

        $datetimeMax = $query->clone()->where('a.lastlogin', '!=', '')->max('a.lastlogin');

        $distinct = UserAttribute::query()
            ->distinct()
            ->select(['name', 'role', 'blocked'])
            ->leftJoin((new UserRole())->getTable() . ' as r', 'r.id', 'role')
            ->when($filterDatetime->count(), fn($query) => $query->whereBetween('lastlogin', $filterDatetime))
            ->get();

        $filterRole = $distinct->keyBy('name')
            ->sortBy('name')
            ->map(fn(UserAttribute $item) => [
                'key' => $item->role,
                'value' => $item->name,
                'selected' => $item->role == $filterRole,
            ])
            ->prepend([
                'key' => '',
                'value' => Lang::get('global.mgrlog_anyall'),
            ], '')
            ->values();

        $filterBlocked = $distinct->keyBy('blocked')
            ->map(fn(UserAttribute $item) => [
                'key' => $item->blocked,
                'value' => $item->blocked ? Lang::get('global.yes') : Lang::get('global.no'),
                'selected' => $item->blocked == $filterBlocked,
            ])
            ->prepend([
                'key' => '',
                'value' => Lang::get('global.mgrlog_anyall'),
            ], '')
            ->sortBy('blocked')
            ->values();

        $columns = [
            [
                'name' => 'id',
                'label' => Lang::get('global.id'),
                'sort' => true,
                'width' => '5rem',
                'style' => [
                    'textAlign' => 'right',
                    'fontWeight' => 'bold',
                ],
            ],
            [
                'name' => 'name',
                'label' => Lang::get('global.name'),
                'sort' => true,
                'style' => [
                    'fontWeight' => '500',
                ],
            ],
            [
                'name' => 'fullname',
                'label' => Lang::get('global.user_full_name'),
                'sort' => true,
            ],
            [
                'name' => 'email',
                'label' => Lang::get('global.email'),
                'sort' => true,
            ],
            [
                'key' => 'rolename',
                'name' => 'role',
                'label' => Lang::get('global.role'),
                'sort' => true,
                'filter' => [
                    'data' => $filterRole,
                ],
                'width' => '10rem',
            ],
            [
                'name' => 'lastlogin',
                'label' => Lang::get('global.user_prevlogin'),
                'sort' => true,
                'filter' => [
                    'type' => 'date',
                    'data' => [
                        'from' => date('Y-m-d', $filterDatetime->first() ?: $datetimeMin),
                        'to' => date('Y-m-d', $filterDatetime->last() ?: $datetimeMax),
                        'min' => date('Y-m-d', $datetimeMin),
                        'max' => date('Y-m-d', $datetimeMax),
                    ],
                ],
                'width' => '12rem',
                'style' => [
                    'textAlign' => 'center',
                ],
            ],
            [
                'name' => 'logincount',
                'label' => Lang::get('global.user_logincount'),
                'sort' => true,
                'width' => '10rem',
                'style' => [
                    'textAlign' => 'center',
                ],
            ],
            [
                'name' => 'blocked',
                'label' => Lang::get('global.user_block'),
                'sort' => true,
                'filter' => [
                    'data' => $filterBlocked,
                ],
                'width' => '7rem',
                'style' => [
                    'textAlign' => 'center',
                ],
                'values' => [
                    0 => $no,
                    1 => $yes,
                ],
            ],
        ];

        return UserResource::collection([
            'data' => [
                'data' => $result->items(),
                'columns' => $columns,
                'pagination' => $this->pagination($result),
                'sorting' => [
                    'order' => $order,
                    'dir' => $dir,
                ],
            ],
            'layout' => $layout->default(),
        ]);
    }

    /**
     * @param UserRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function getActive(UserRequest $request): AnonymousResourceCollection
    {
        $result = ActiveUserSession::query()
            ->select(['internalKey', 'internalKey as id', 'ip', 'lasthit'])
            ->with('user', fn($q) => $q->select(['id', 'username']))
            ->orderByDesc('lasthit')
            ->paginate(Config::get('global.number_of_results'));

        return UserResource::collection([
            'data' => [
                'data' => $result->items(),
                'columns' => [
                    [
                        'key' => 'id',
                        'label' => 'ID',
                        'style' => [
                            'textAlign' => 'right',
                        ],
                    ],
                    [
                        'name' => 'user.username',
                        'label' => Lang::get('global.user'),
                    ],
                    [
                        'name' => 'ip',
                        'label' => 'IP',
                        'style' => [
                            'textAlign' => 'center',
                        ],
                    ],
                    [
                        'name' => 'lasthit',
                        'label' => Lang::get('global.onlineusers_lasthit'),
                        'style' => [
                            'textAlign' => 'center',
                        ],
                    ],
                ],
                'pagination' => $this->pagination($result),
            ],
        ]);
    }
}
