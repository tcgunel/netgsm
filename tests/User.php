<?php

namespace TCGunel\Netgsm\Tests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TCGunel\Netgsm\Models\NetgsmLog;
use TCGunel\Netgsm\Traits\NetgsmLoggable;

/**
 * Class User
 * @package TCGunel\Netgsm\Tests
 *
 * @property NetgsmLog[] $netgsm_logs
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, NetgsmLoggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
