<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class SnippetResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array | JsonSerializable | Arrayable
    {
        return parent::toArray($request);
    }
}
