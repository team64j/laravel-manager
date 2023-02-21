<?php

namespace Team64j\LaravelManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Throwable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property string $username
 */
class User extends \Illuminate\Foundation\Auth\User implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $rememberTokenName = 'access_token';

    /**
     * @var string[]
     */
    protected $hidden = [
        'password',
        'cachepwd',
        'verified_key',
        'refresh_token',
        'access_token',
        'valid_to',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'username',
        'password',
        'cachepwd',
        'verified_key',
        'refresh_token',
        'access_token',
        'valid_to',
    ];

    /**
     * @return HasOne
     */
    public function attributes(): HasOne
    {
        return $this->hasOne(UserAttribute::class, 'internalKey', 'id');
    }

    /**
     * @return HasMany
     */
    public function settings(): HasMany
    {
        return $this->hasMany(UserSetting::class, 'user', 'id');
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = 'https://example.com/reset-password?token=' . $token;

        $this->notify(new ResetPasswordNotification($url));
        //Notification::send($this, new ResetPasswordNotification());
    }

    /**
     * @param $driver
     * @param $notification
     *
     * @return string[]
     */
    public function routeNotificationForMail($driver, $notification = null): array
    {
        /** @var UserAttribute $attributes */
        $attributes = UserAttribute::query()
            ->firstWhere('internalKey', $this->getKey());

        return [$attributes->email => $this->username];
    }

    /**
     * @param $permissions
     *
     * @return bool
     */
    public function hasPermissions($permissions): bool
    {
        return $this['attributes']
                ->rolePermissions
                ->whereIn('permission', (array) $permissions)
                ->count() == count($permissions);
    }

    /**
     * @param $permissions
     *
     * @return bool
     * @throws Throwable
     */
    public function hasPermissionsOrFail($permissions): bool
    {
        return throw_unless($this->hasPermissions($permissions), __('global.error_no_privileges'));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
