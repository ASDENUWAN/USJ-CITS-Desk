<?php

namespace App\Providers;


use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(
            function (User $user, string $ability): ?bool {
                return $user->hasRole(
                    'Super Admin',
                    'web'
                ) ? true : null;
            }
        );
    }
}
