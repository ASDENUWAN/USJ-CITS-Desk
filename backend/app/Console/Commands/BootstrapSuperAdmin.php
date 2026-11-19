<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use RuntimeException;
use Throwable;

class BootstrapSuperAdmin extends Command
{
    protected $signature = 'app:bootstrap-super-admin
        {--force : Allow execution in production}';

    protected $description =
    'Securely create the initial Super Admin account';

    public function handle(): int
    {
        // Require explicit approval in production
        if (app()->isProduction() && ! $this->option('force')) {
            $this->error(
                'Production requires the --force option.'
            );

            return self::FAILURE;
        }

        // Ensure the role was seeded
        $roleExists = Role::query()
            ->where('name', 'Super Admin')
            ->where('guard_name', 'web')
            ->exists();

        if (! $roleExists) {
            $this->error(
                'Run the AccessControlSeeder first.'
            );

            return self::FAILURE;
        }

        // Prevent normal re-execution
        if (User::role('Super Admin')->exists()) {
            $this->error(
                'A Super Admin account already exists.'
            );

            return self::FAILURE;
        }

        // Collect account details
        $data = [
            'username' => trim(
                (string) $this->ask('Username')
            ),

            'email' => trim(
                (string) $this->ask('Email (optional)')
            ) ?: null,

            'contact_no' => trim(
                (string) $this->ask('Contact number (optional)')
            ) ?: null,

            'password' => $this->secret(
                'Strong password'
            ),

            'password_confirmation' => $this->secret(
                'Confirm password'
            ),
        ];

        // Validate before saving
        $validator = Validator::make($data, [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/\A[A-Za-z0-9._-]+\z/',
                Rule::unique('users', 'username'),
            ],

            'email' => [
                'nullable',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'contact_no' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(4)
                    ->max(128)
                // ->mixedCase()
                // ->numbers()
                // ->symbols(),
            ],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        try {
            DB::transaction(function () use ($data) {

                // Lock role during initial account creation
                $role = Role::query()
                    ->where('name', 'Super Admin')
                    ->where('guard_name', 'web')
                    ->lockForUpdate()
                    ->firstOrFail();

                // Recheck while holding the lock
                if (User::role($role)->exists()) {
                    throw new RuntimeException(
                        'Super Admin already exists.'
                    );
                }

                // User model hashes password
                // and defaults name to username
                $user = User::create([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'contact_no' => $data['contact_no'],
                    'password' => $data['password'],
                ]);

                // Assign protected Super Admin role
                $user->assignRole($role);
            });
        } catch (Throwable $exception) {
            $this->error(
                'Super Admin creation failed. '
                    . 'Check your application logs.'
            );

            return self::FAILURE;
        }

        $this->info(
            'Super Admin account created successfully.'
        );

        return self::SUCCESS;
    }
}
