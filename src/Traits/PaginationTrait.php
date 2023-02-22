<?php

namespace Team64j\LaravelManager\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Lang;

trait PaginationTrait
{
    /**
     * @param LengthAwarePaginator $result
     *
     * @return array
     */
    protected function pagination(LengthAwarePaginator $result): array
    {
        $current = min($result->currentPage(), $result->lastPage());
        $from = $current > 1 ? (($current - 1) * $result->perPage()) + 1 : 1;
        $to = $current > 1 ? $result->perPage() * $current : $result->perPage();

        if ($to > $result->total()) {
            $to = $result->total();
        }

        return [
            'total' => $result->total(),
            'prev' => $result->previousPageUrl(),
            'next' => $result->nextPageUrl(),
            'current' => $current,
            'per' => $result->perPage(),
            'info' => Lang::get('global.showing') . ' ' . $from . '-' . $to . '/' . $result->total(),
        ];
    }
}
