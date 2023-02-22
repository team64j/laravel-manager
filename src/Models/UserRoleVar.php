<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRoleVar extends Model
{
    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $casts = [
        'tmplvarid' => 'int',
        'roleid' => 'int',
        'rank' => 'int',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'tmplvarid',
        'roleid',
        'rank',
    ];

    /**
     * @return BelongsTo
     */
    public function tmplvar(): BelongsTo
    {
        return $this->belongsTo(SiteTmplvar::class, 'tmplvarid', 'id');
    }
}
