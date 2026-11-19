<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use LogicException;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * Use one permission guard for our application.
     */
    protected string $guard_name = 'web';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'contact_no',
        'password',
    ];

    /**
     * Hide sensitive information from API responses.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Model events.
     */
    protected static function booted(): void
    {
        // Default display name = username
        static::creating(function (User $user): void {
            if (blank($user->name)) {
                $user->name = $user->username;
            }
        });

        // Username cannot change after creation
        static::updating(function (User $user): void {
            if ($user->isDirty('username')) {
                throw new LogicException(
                    'Username cannot be changed after account creation.'
                );
            }
        });
    }
}
