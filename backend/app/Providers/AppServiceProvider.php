<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * Super Admin authorization.
         */
        Gate::before(
            function (User $user, string $ability): ?bool {
                return $user->hasRole(
                    'Super Admin',
                    'web'
                ) ? true : null;
            }
        );

        /**
         * Login rate limiting.
         */
        RateLimiter::for('login', function (Request $request) {
            $username = strtolower(trim(
                (string) $request->input('username', '')
            ));

            $ip = $request->ip();

            return [
                Limit::perMinute(5)->by(
                    'login-user-ip:' . hash(
                        'sha256',
                        $username . '|' . $ip
                    )
                ),

                Limit::perMinute(30)->by(
                    'login-ip:' . $ip
                ),
            ];
        });
    }
}
