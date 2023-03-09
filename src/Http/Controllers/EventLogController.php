<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Team64j\LaravelEvolution\Models\EventLog;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Team64j\LaravelManager\Http\Requests\EventLogRequest;
use Team64j\LaravelManager\Http\Resources\EventLogResource;
use Team64j\LaravelManager\Layouts\EventLogLayout;

class EventLogController extends Controller
{
    /**
     * @param EventLogRequest $request
     * @param EventLogLayout $layout
     *
     * @return AnonymousResourceCollection
     */
    public function index(EventLogRequest $request, EventLogLayout $layout): AnonymousResourceCollection
    {
        $filterType = $request->input('type');
        $filterUser = $request->input('user', '');
        $filterEventId = $request->input('eventid');

        /** @var Collection $filterDatetime */
        $filterDatetime = $request->collect('createdon')->map(function ($item, $index) {
            if ($index) {
                $item .= ' 23:59:59';
            } else {
                $item .= ' 00:00:00';
            }

            return strtotime($item);
        });

        $logTypes = [
            1 => Lang::get('global.information'),
            2 => Lang::get('global.warning'),
            3 => Lang::get('global.error'),
        ];

        $query = EventLog::query()
            ->when($filterType, fn($query) => $query->where('type', $filterType))
            ->when($filterUser != '', fn($query) => $query->where('user', $filterUser))
            ->when($filterEventId, fn($query) => $query->where('eventid', $filterEventId));

        $result = $query->clone()
            ->select(['id', 'type', 'source', 'createdon', 'eventid', 'user'])
            ->with('users', fn($query) => $query->select('id', 'username'))
            ->when($filterDatetime->count(), fn($query) => $query->whereBetween('createdon', $filterDatetime))
            ->orderByDesc('id')
            ->simplePaginate(Config::get('global.number_of_results'))
            ->appends($request->all());

        $datetime = $query->clone()
            ->selectRaw('MIN(createdon) AS timestamp_from, MAX(createdon) AS timestamp_to')
            ->first();

        $distinct = $query->clone()
            ->select('type', 'user', 'eventid')
            ->with('users', fn($query) => $query->select('id', 'username'))
            ->when($filterDatetime->count(), fn($query) => $query->whereBetween('createdon', $filterDatetime))
            ->distinct()
            ->get();

        $filterType = $distinct->keyBy('type')
            ->sortBy('type')
            ->map(fn(EventLog $item) => [
                'key' => $item->type,
                'value' => $logTypes[$item->type] ?? 1,
                'selected' => $item->type == $filterType,
            ])
            ->prepend([
                'key' => '',
                'value' => Lang::get('global.mgrlog_anyall'),
            ], '*')
            ->filter(fn($item) => $item['value'])
            ->values();

        $filterUser = $distinct->keyBy('user')
            ->sortBy('user')
            ->map(fn(EventLog $item) => [
                'key' => $item->user,
                'value' => $item->users ? $item->users->username : '-',
                'selected' => $item->user == $filterUser,
            ])
            ->prepend([
                'key' => '',
                'value' => Lang::get('global.mgrlog_anyall'),
            ], '*')
            ->filter(fn($item) => $item['value'])
            ->values();

        $filterEventId = $distinct->keyBy('eventid')
            ->sortBy('eventid')
            ->map(fn(EventLog $item) => [
                'key' => $item->eventid,
                'value' => $item->eventid,
                'selected' => $item->eventid == $filterEventId,
            ])
            ->prepend([
                'key' => '',
                'value' => Lang::get('global.mgrlog_anyall'),
            ], '*')
            ->filter(fn($item) => $item['value'])
            ->values();

        return EventLogResource::collection(
            [
                'data' => [
                    'data' => $result->items(),
                    'columns' => [
                        [
                            'name' => 'type',
                            'label' => Lang::get('global.type'),
                            'filter' => [
                                'data' => $filterType,
                            ],
                            'values' => (object) [
                                1 => '<i class="fa fa-info-circle text-blue-500"></i>',
                                2 => '<i class="fa fa-exclamation-triangle text-amber-400"></i>',
                                3 => '<i class="fa fa-times-circle text-rose-500"></i>',
                            ],
                            'style' => [
                                'textAlign' => 'center',
                                'width' => '10rem',
                            ],
                        ],
                        [
                            'name' => 'source',
                            'label' => Lang::get('global.source'),
                        ],
                        [
                            'name' => 'createdon',
                            'label' => Lang::get('global.date'),
                            'filter' => [
                                'type' => 'date',
                                'data' => [
                                    'from' => date('Y-m-d', $filterDatetime->first() ?: $datetime->timestamp_from),
                                    'to' => date('Y-m-d', $filterDatetime->last() ?: $datetime->timestamp_to),
                                    'min' => date('Y-m-d', $datetime->timestamp_from),
                                    'max' => date('Y-m-d', $datetime->timestamp_to),
                                ],
                            ],
                            'style' => [
                                'textAlign' => 'center',
                                'width' => '20rem',
                            ],
                        ],
                        [
                            'name' => 'eventid',
                            'label' => Lang::get('global.event_id'),
                            'filter' => [
                                'data' => $filterEventId,
                            ],
                            'style' => [
                                'textAlign' => 'center',
                                'width' => '10rem',
                            ],
                        ],
                        [
                            'name' => 'user',
                            'key' => 'users.username',
                            'label' => Lang::get('global.user'),
                            'filter' => [
                                'data' => $filterUser,
                            ],
                            'style' => [
                                'width' => '20rem',
                            ],
                        ],
                    ],
                ],
            ],
        )->additional([
            'layout' => $layout->default(),
        ]);
    }

    /**
     * @param EventLogRequest $request
     * @param string $eventlog
     *
     * @return EventLogResource
     */
    public function show(EventLogRequest $request, string $eventlog): EventLogResource
    {
        return new EventLogResource(
            EventLog::query()
                ->with('users', fn($query) => $query->select('id', 'username'))
                ->find($eventlog)
        );
    }
}
