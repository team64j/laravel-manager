<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserRole extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $casts = [
        'frames' => 'int',
        'home' => 'int',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return BelongsToMany
     */
    public function tvs(): BelongsToMany
    {
        return $this->belongsToMany(
            SiteTmplvar::class,
            (new UserRoleVar())->getTable(),
            'roleid',
            'tmplvarid'
        )->withPivot('rank')
            ->orderBy('pivot_rank', 'ASC');
    }
}
