<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property string $lang_key
 */
class PermissionsGroups extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'lang_key',
    ];

    /**
     * @return HasMany
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permissions::class, 'group_id', 'id');
    }
}
