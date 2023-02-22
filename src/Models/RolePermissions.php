<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RolePermissions extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'permission',
        'role_id',
    ];

    /**
     * @return HasOne
     */
    public function permissions(): HasOne
    {
        return $this->hasOne(Permissions::class, 'key', 'permission');
    }
}
