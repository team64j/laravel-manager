<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Traits;

use DateTimeInterface;
use Illuminate\Support\Facades\Config;

trait TimeMutatorTrait
{
    /**
     * @param int|string|null $value
     * @param string|null $default
     *
     * @return string
     */
    protected function convertDateTime(int | string $value = null, string $default = null): string
    {
        if (!$value) {
            $value = $default;
        }

        if (!$value) {
            return '';
        }

        return $this->asDateTime($value)->format('Y-m-d H:i:s');
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param DateTimeInterface $date
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        $format = str_replace(
            ['dd', 'mm', 'YYYY',],
            ['d', 'm', 'Y',],
            Config::get('global.datetime_format', 'Y-m-d')
        );

        return $date->getTimestamp() ? $date->format($format . ' H:i:s') : '';
    }
}
