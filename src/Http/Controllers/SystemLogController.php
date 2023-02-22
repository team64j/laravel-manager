<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use App\Models\ManagerLog;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\SystemLogRequest;
use Team64j\LaravelManager\Http\Resources\SystemLogResource;
use Team64j\LaravelManager\Traits\PaginationTrait;

class SystemLogController extends Controller
{
    use PaginationTrait;

    /**
     * @param SystemLogRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(SystemLogRequest $request): AnonymousResourceCollection
    {
        $order = $request->input('order', 'id');
        $dir = $request->input('dir', 'desc');
        $filterUsername = $request->input('username');
        $filterAction = $request->input('action');
        $filterItemId = $request->input('itemid');
        $filterItemName = $request->input('itemname');

        /** @var Collection $filterDatetime */
        $filterDatetime = $request->collect('timestamp')->map(function ($item, $index) {
            if ($index) {
                $item .= ' 23:59:59';
            } else {
                $item .= ' 00:00:00';
            }

            return strtotime($item);
        });

        $fields = ['id', 'username', 'action', 'message', 'itemid', 'itemname', 'timestamp', 'ip', 'useragent'];

        if (!in_array($order, $fields)) {
            $order = 'id';
        }

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }

        $query = ManagerLog::query()
            ->when($filterUsername, fn($query) => $query->where('username', $filterUsername))
            ->when($filterAction, fn($query) => $query->where('action', $filterAction))
            ->when($filterItemId, fn($query) => $query->where('itemid', $filterItemId))
            ->when($filterItemName, fn($query) => $query->where('itemname', $filterItemName));

        $result = $query->clone()
            ->select($fields)
            ->when($filterDatetime->count(), fn($query) => $query->whereBetween('timestamp', $filterDatetime))
            ->orderBy($order, $dir)
            ->paginate(Config::get('global.number_of_results'))
            ->appends($request->all());

        $datetime = $query->clone()
            ->selectRaw('MIN(timestamp) AS timestamp_from, MAX(timestamp) AS timestamp_to')
            ->first();

        $distinct = $query->clone()
            ->select('username', 'action', 'message', 'itemid', 'itemname')
            ->when($filterDatetime->count(), fn($query) => $query->whereBetween('timestamp', $filterDatetime))
            ->distinct()
            ->get();

        $filterUsername = $distinct->keyBy('username')
            ->sortBy('username')
            ->map(fn(ManagerLog $item) => [
                'key' => $item->username,
                'value' => $item->username,
                'selected' => $item->username == $filterUsername,
            ])
            ->prepend([
                'key' => '',
                'value' => Lang::get('global.mgrlog_anyall'),
            ], 0)
            ->filter(fn($item) => $item['value'])
            ->values();

        $filterAction = $distinct->keyBy('action')
            ->sortBy('action')
            ->map(fn(ManagerLog $item) => [
                'key' => $item->action,
                'value' => $item->action . ' - ' . $item->message,
                'selected' => $item->action == $filterAction,
            ])
            ->prepend([
                'key' => '',
                'value' => Lang::get('global.mgrlog_anyall'),
            ], 0)
            ->filter(fn($item) => $item['value'])
            ->values();

        $filterItemId = $distinct->keyBy('itemid')
            ->sortBy('itemid')
            ->map(fn(ManagerLog $item) => [
                'key' => $item->itemid,
                'value' => $item->itemid,
                'selected' => $item->itemid == $filterItemId,
            ])
            ->prepend([
                'key' => '',
                'value' => Lang::get('global.mgrlog_anyall'),
            ], 0)
            ->filter(fn($item) => $item['value'])
            ->values();

        $filterItemName = $distinct->keyBy('itemname')
            ->sortBy('itemname', SORT_FLAG_CASE | SORT_NATURAL)
            ->map(fn(ManagerLog $item) => [
                'key' => $item->itemname,
                'value' => $item->itemname,
                'selected' => $item->itemname == $filterItemName,
            ])
            ->prepend([
                'key' => '',
                'value' => Lang::get('global.mgrlog_anyall'),
            ], 0)
            ->filter(fn($item) => $item['value'])
            ->values();

        return SystemLogResource::collection([
            'data' => [
                'data' => $result->items(),
                'sorting' => [
                    'order' => $order,
                    'dir' => $dir,
                ],
                'columns' => [
                    [
                        'name' => 'username',
                        'label' => Lang::get('global.mgrlog_user'),
                        'sort' => true,
                        'filter' => [
                            'data' => $filterUsername,
                        ],
                    ],
                    [
                        'name' => 'action',
                        'label' => Lang::get('global.mgrlog_actionid'),
                        'sort' => true,
                        'filter' => [
                            'data' => $filterAction,
                            'placeholder' => Lang::get('global.mgrlog_action'),
                        ],
                    ],
                    [
                        'name' => 'message',
                        'label' => Lang::get('global.mgrlog_msg'),
                        'sort' => true,
                    ],
                    [
                        'name' => 'itemid',
                        'label' => Lang::get('global.mgrlog_itemid'),
                        'sort' => true,
                        'filter' => [
                            'data' => $filterItemId,
                        ],
                    ],
                    [
                        'name' => 'itemname',
                        'label' => Lang::get('global.mgrlog_itemname'),
                        'sort' => true,
                        'filter' => [
                            'data' => $filterItemName,
                        ],
                    ],
                    [
                        'name' => 'timestamp',
                        'label' => Lang::get('global.mgrlog_time'),
                        'sort' => true,
                        'filter' => [
                            'type' => 'date',
                            'data' => [
                                'from' => date('Y-m-d', $filterDatetime->first() ?: $datetime->timestamp_from),
                                'to' => date('Y-m-d', $filterDatetime->last() ?: $datetime->timestamp_to),
                                'min' => date('Y-m-d', $datetime->timestamp_from),
                                'max' => date('Y-m-d', $datetime->timestamp_to),
                            ],
                        ],
                    ],
                    [
                        'name' => 'ip',
                        'label' => 'IP',
                        //'sort' => true,
                    ],
                    [
                        'name' => 'useragent',
                        'label' => 'USER_AGENT',
                        'sort' => true,
                    ],
                ],
                'pagination' => $this->pagination($result),
            ],
        ]);
    }
}
