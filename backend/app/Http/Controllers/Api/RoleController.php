<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\SaveRoleRequest;
use App\Http\Requests\SyncRolePermissionsRequest;
use App\Support\RolePermissionGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * GET /api/roles
     */
    public function index(): JsonResponse
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->with('permissions')
            ->orderBy('name')
            ->paginate(20);

        return response()->json([
            'data' => $roles->getCollection()
                ->map(
                    fn(Role $role) =>
                    $this->formatRole($role)
                ),

            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'total' => $roles->total(),
            ],
        ]);
    }

    /**
     * POST /api/roles
     */
    public function store(
        SaveRoleRequest $request
    ): JsonResponse {
        $role = Role::create([
            'name' => $request->validated('name'),
            'guard_name' => 'web',
        ]);

        return response()->json([
            'message' => 'Role created successfully.',
            'data' => $this->formatRole(
                $role->load('permissions')
            ),
        ], 201);
    }

    /**
     * GET /api/roles/{role}
     */
    public function show(Role $role): JsonResponse
    {
        abort_unless($role->guard_name === 'web', 404);

        return response()->json([
            'data' => $this->formatRole(
                $role->load('permissions')
            ),
        ]);
    }

    /**
     * PUT/PATCH /api/roles/{role}
     */
    public function update(
        SaveRoleRequest $request,
        Role $role,
        RolePermissionGuard $guard
    ): JsonResponse {
        $guard->assertManageableRole(
            $request->user(),
            $role
        );

        $role->update([
            'name' => $request->validated('name'),
        ]);

        return response()->json([
            'message' => 'Role updated successfully.',
            'data' => $this->formatRole(
                $role->load('permissions')
            ),
        ]);
    }

    /**
     * PUT /api/roles/{role}/permissions
     */
    public function syncPermissions(
        SyncRolePermissionsRequest $request,
        Role $role,
        RolePermissionGuard $guard
    ): JsonResponse {
        DB::transaction(function () use (
            $request,
            $role,
            $guard
        ): void {
            $lockedRole = Role::query()
                ->whereKey($role->id)
                ->lockForUpdate()
                ->firstOrFail();

            $guard->assertManageableRole(
                $request->user(),
                $lockedRole
            );

            $permissions = Permission::query()
                ->where('guard_name', 'web')
                ->whereIn(
                    'id',
                    $request->validated('permission_ids')
                )
                ->get();

            $guard->assertDelegatable(
                $request->user(),
                $permissions
            );

            $lockedRole->syncPermissions($permissions);
        });

        return response()->json([
            'message' => 'Role permissions updated successfully.',
            'data' => $this->formatRole(
                $role->load('permissions')
            ),
        ]);
    }

    /**
     * DELETE /api/roles/{role}
     */
    public function destroy(
        Request $request,
        Role $role,
        RolePermissionGuard $guard
    ): JsonResponse {
        DB::transaction(function () use (
            $request,
            $role,
            $guard
        ): void {
            $lockedRole = Role::query()
                ->whereKey($role->id)
                ->lockForUpdate()
                ->firstOrFail();

            $guard->assertManageableRole(
                $request->user(),
                $lockedRole
            );

            $guard->assertRoleUnused($lockedRole);

            $lockedRole->delete();
        });

        return response()->json([
            'message' => 'Role deleted successfully.',
        ]);
    }

    /**
     * Standard role API response.
     */
    private function formatRole(Role $role): array
    {
        $role->loadMissing('permissions');

        return [
            'id' => $role->id,
            'name' => $role->name,

            'permissions' => $role->permissions
                ->sortBy('name')
                ->values()
                ->map(fn(Permission $permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ])
                ->all(),
        ];
    }
}
