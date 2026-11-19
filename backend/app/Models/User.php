<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LogicException;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Fields allowed for mass assignment.
     *
     * Controllers must still validate and authorize
     * which fields users are allowed to change.
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'contact_no',
        'password',
    ];

    /**
     * Hide sensitive fields from JSON responses.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Automatically cast and hash attributes.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Model lifecycle events.
     */
    protected static function booted(): void
    {
        /**
         * Set the default name to the username
         * when a new user is created.
         */
        static::creating(function (User $user): void {
            if (blank($user->name)) {
                $user->name = $user->username;
            }
        });

        /**
         * Prevent changing the username
         * after the account is created.
         */
        static::updating(function (User $user): void {
            if ($user->isDirty('username')) {
                throw new LogicException(
                    'Username cannot be changed after account creation.'
                );
            }
        });
    }
}
