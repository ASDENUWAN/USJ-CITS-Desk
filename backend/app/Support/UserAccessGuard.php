<?php

namespace App\Support;


use App\Models\User;
use Illuminate\Support\Collection;

class UserAccessGuard
{
    /**
     * Check whether an actor may manage a user.
     */
    public function assertManageable(
        User $actor,
        User $target
    ): void {
        // Protect Super Admin accounts
        abort_if(
            $target->hasRole('Super Admin', 'web'),
            403,
            'Super Admin accounts are protected.'
        );

        // Prevent self-management through admin APIs
        abort_if(
            $actor->id === $target->id,
            403,
            'Use the profile API for your own account.'
        );

        // Super Admin can manage other normal accounts
        if ($actor->hasRole('Super Admin', 'web')) {
            return;
        }

        // Normal Admins cannot manage users with
        // permissions they do not possess.
        $actorPermissionIds = $actor
            ->getAllPermissions()
            ->pluck('id')
            ->all();

        $targetPermissionIds = $target
            ->getAllPermissions()
            ->pluck('id')
            ->all();

        abort_if(
            count(array_diff(
                $targetPermissionIds,
                $actorPermissionIds
            )) > 0,
            403,
            'You cannot manage this user.'
        );
    }

    /**
     * Check roles before assigning them.
     */
    public function assertAssignableRoles(
        User $actor,
        Collection $roles,
        RolePermissionGuard $permissionGuard
    ): void {
        // Never assign Super Admin through
        // the general role-management API.
        foreach ($roles as $role) {
            abort_if(
                strcasecmp(
                    $role->name,
                    'Super Admin'
                ) === 0,
                403,
                'Super Admin role assignment is protected.'
            );
        }

        if ($actor->hasRole('Super Admin', 'web')) {
            return;
        }

        // Check every permission included in
        // the requested roles.
        $permissions = $roles
            ->flatMap(
                fn($role) => $role->permissions
            )
            ->unique('id')
            ->values();

        $permissionGuard->assertDelegatable(
            $actor,
            $permissions
        );
    }
}
