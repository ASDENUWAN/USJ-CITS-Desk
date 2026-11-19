<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        // Reset permission cache
        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();

        // Initial system permissions
        $permissions = [
            // User management
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            // Role management
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'roles.assign',

            // Permission management
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',
            'permissions.assign',
        ];

        // Create permissions safely
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create the protected Super Admin role
        Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        // Reset cache after seeding
        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }
}
