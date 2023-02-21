<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Models;

use App\Models\RolePermissions;
use App\Models\UserRole;
use App\Traits\Models\TimeMutatorTrait;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $internalKey
 * @property string $fullname
 * @property string $middle_name
 * @property string $last_name
 * @property int $role
 * @property string $email
 * @property string $phone
 * @property string $mobilephone
 * @property int $verified
 * @property int $blocked
 * @property int $blockeduntil
 * @property int $blockedafter
 * @property int $logincount
 * @property int $lastlogin
 * @property int $thislogin
 * @property int $failedlogincount
 * @property string $sessionid
 * @property int $dob
 * @property int $gender
 * @property string $country
 * @property string $street
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $fax
 * @property string $photo
 * @property string $comment
 * @property int $createdon
 * @property int $editedon
 * @property User $user
 *
 * Virtual
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-write string $username
 */
class UserAttribute extends Model
{
    use TimeMutatorTrait;

    public const CREATED_AT = 'createdon';
    public const UPDATED_AT = 'editedon';

    /**
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * @var string[]
     */
    protected $casts = [
        'internalKey' => 'int',
        'role' => 'int',
        'verified' => 'int',
        'blocked' => 'int',
        'blockeduntil' => 'int',
        'blockedafter' => 'int',
        'logincount' => 'int',
        'lastlogin' => 'int',
        //'lastlogin' => 'datetime',
        'thislogin' => 'int',
        'failedlogincount' => 'int',
        'dob' => 'int',
        'gender' => 'int',
        'createdon' => 'int',
        'editedon' => 'int',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'role',
    ];

    /**
     * @var int[]
     */
    protected $attributes = [
        'role' => 0,
        'verified' => 1,
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'internalKey',
        'fullname',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'mobilephone',
        'verified',
        'blocked',
        'blockeduntil',
        'blockedafter',
        'logincount',
        'lastlogin',
        'thislogin',
        'failedlogincount',
        'sessionid',
        'dob',
        'gender',
        'country',
        'street',
        'city',
        'state',
        'zip',
        'fax',
        'photo',
        'comment',
    ];

    /**
     * @param $value
     *
     * @return string
     */
    public function getLastloginAttribute($value): string
    {
        return $this->convertDateTime($value);
    }

    /**
     * @return HasMany
     */
    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermissions::class, 'role_id', 'role');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'internalKey', 'id');
    }

    /**
     * @return HasOne
     */
    public function userRole(): HasOne
    {
        return $this->hasOne(UserRole::class, 'id', 'role');
    }
}
