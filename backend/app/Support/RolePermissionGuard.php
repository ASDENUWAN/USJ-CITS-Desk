<?php

namespace App\Support;


use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionGuard
{
    private const CORE_PERMISSIONS = [
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',
        'roles.assign',
        'permissions.view',
        'permissions.create',
        'permissions.update',
        'permissions.delete',
        'permissions.assign',
    ];

    /**
     * Check whether the actor may manage a role.
     */
    public function assertManageableRole(
        User $actor,
        Role $role
    ): void {
        abort_unless($role->guard_name === 'web', 404);

        // Nobody can change the protected role
        // through the normal management API.
        abort_if(
            strcasecmp($role->name, 'Super Admin') === 0,
            403,
            'The Super Admin role is protected.'
        );

        if ($actor->hasRole('Super Admin', 'web')) {
            return;
        }

        // An admin cannot modify a role containing
        // permissions they do not possess.
        $ownedIds = $actor->getAllPermissions()
            ->pluck('id')
            ->all();

        $roleIds = $role->permissions()
            ->pluck('permissions.id')
            ->all();

        abort_if(
            count(array_diff($roleIds, $ownedIds)) > 0,
            403,
            'You cannot manage a role with permissions you do not have.'
        );
    }

    /**
     * Prevent granting permissions beyond the
     * currently authenticated user's authority.
     */
    public function assertDelegatable(
        User $actor,
        Collection $permissions
    ): void {
        if ($actor->hasRole('Super Admin', 'web')) {
            return;
        }

        $ownedIds = $actor->getAllPermissions()
            ->pluck('id')
            ->all();

        $requestedIds = $permissions
            ->pluck('id')
            ->all();

        abort_if(
            count(array_diff($requestedIds, $ownedIds)) > 0,
            403,
            'You cannot grant permissions you do not have.'
        );
    }

    /**
     * Prevent deleting roles currently in use.
     */
    public function assertRoleUnused(Role $role): void
    {
        $assigned = DB::table(
            config('permission.table_names.model_has_roles')
        )
            ->where('role_id', $role->id)
            ->exists();

        abort_if(
            $assigned,
            409,
            'Unassign this role from all users before deleting it.'
        );
    }

    /**
     * Protect core permissions and prevent
     * deleting permissions that are in use.
     */
    public function assertPermissionDeletable(
        Permission $permission
    ): void {
        abort_unless(
            $permission->guard_name === 'web',
            404
        );

        abort_if(
            in_array(
                $permission->name,
                self::CORE_PERMISSIONS,
                true
            ),
            403,
            'Core system permissions cannot be deleted.'
        );

        $assignedToRole = DB::table(
            config('permission.table_names.role_has_permissions')
        )
            ->where('permission_id', $permission->id)
            ->exists();

        $assignedToUser = DB::table(
            config('permission.table_names.model_has_permissions')
        )
            ->where('permission_id', $permission->id)
            ->exists();

        abort_if(
            $assignedToRole || $assignedToUser,
            409,
            'Remove this permission from all roles and users before deleting it.'
        );
    }
}
