<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\SyncUserRolesRequest;
use App\Http\Requests\SyncUserPermissionsRequest;
use App\Models\User;
use App\Support\UserAccessGuard;
use App\Support\RolePermissionGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * GET /api/users
     */
    public function index(): JsonResponse
    {
        $users = User::query()
            ->with(['roles.permissions', 'permissions'])
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json([
            'data' => $users->getCollection()
                ->map(
                    fn(User $user) =>
                    $this->formatUser($user)
                )
                ->values(),

            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * POST /api/users
     */
    public function store(
        StoreUserRequest $request
    ): JsonResponse {
        $data = $request->safe()->only([
            'username',
            'email',
            'contact_no',
            'password',
        ]);

        $user = User::create($data);

        return response()->json([
            'message' => 'User created successfully.',
            'data' => $this->formatUser($user),
        ], 201);
    }

    /**
     * GET /api/users/{user}
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => $this->formatUser($user),
        ]);
    }

    /**
     * PATCH /api/users/{user}
     */
    public function update(
        UpdateUserRequest $request,
        User $user,
        UserAccessGuard $guard
    ): JsonResponse {
        $updatedUser = DB::transaction(
            function () use ($request, $user, $guard) {
                $target = User::query()
                    ->whereKey($user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $guard->assertManageable(
                    $request->user(),
                    $target
                );

                $target->update(
                    $request->safe()->only([
                        'name',
                        'email',
                        'contact_no',
                    ])
                );

                return $target;
            }
        );

        return response()->json([
            'message' => 'User updated successfully.',
            'data' => $this->formatUser($updatedUser),
        ]);
    }

    /**
     * PUT /api/users/{user}/roles
     */
    public function syncRoles(
        SyncUserRolesRequest $request,
        User $user,
        UserAccessGuard $userGuard,
        RolePermissionGuard $permissionGuard
    ): JsonResponse {
        $target = DB::transaction(
            function () use (
                $request,
                $user,
                $userGuard,
                $permissionGuard
            ) {
                $target = User::query()
                    ->whereKey($user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $actor = $request->user();

                $userGuard->assertManageable(
                    $actor,
                    $target
                );

                $ids = $request->validated('role_ids');

                $roles = Role::query()
                    ->where('guard_name', 'web')
                    ->whereIn('id', $ids)
                    ->lockForUpdate()
                    ->with('permissions')
                    ->get();

                abort_if(
                    $roles->count() !== count($ids),
                    422,
                    'One or more roles no longer exist.'
                );

                $userGuard->assertAssignableRoles(
                    $actor,
                    $roles,
                    $permissionGuard
                );

                $target->syncRoles($roles);

                return $target;
            }
        );

        return response()->json([
            'message' => 'User roles updated successfully.',
            'data' => $this->formatUser(
                $target->fresh()
            ),
        ]);
    }

    /**
     * PUT /api/users/{user}/permissions
     */
    public function syncPermissions(
        SyncUserPermissionsRequest $request,
        User $user,
        UserAccessGuard $userGuard,
        RolePermissionGuard $permissionGuard
    ): JsonResponse {
        $target = DB::transaction(
            function () use (
                $request,
                $user,
                $userGuard,
                $permissionGuard
            ) {
                $target = User::query()
                    ->whereKey($user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $actor = $request->user();

                $userGuard->assertManageable(
                    $actor,
                    $target
                );

                $ids = $request->validated(
                    'permission_ids'
                );

                $permissions = Permission::query()
                    ->where('guard_name', 'web')
                    ->whereIn('id', $ids)
                    ->lockForUpdate()
                    ->get();

                abort_if(
                    $permissions->count() !== count($ids),
                    422,
                    'One or more permissions no longer exist.'
                );

                $permissionGuard->assertDelegatable(
                    $actor,
                    $permissions
                );

                $target->syncPermissions($permissions);

                return $target;
            }
        );

        return response()->json([
            'message' => 'User permissions updated successfully.',
            'data' => $this->formatUser(
                $target->fresh()
            ),
        ]);
    }

    /**
     * DELETE /api/users/{user}
     */
    public function destroy(
        Request $request,
        User $user,
        UserAccessGuard $guard
    ): JsonResponse {
        DB::transaction(
            function () use ($request, $user, $guard) {
                $target = User::query()
                    ->whereKey($user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $guard->assertManageable(
                    $request->user(),
                    $target
                );

                // Revoke any API tokens
                $target->tokens()->delete();

                // Remove stored database sessions
                DB::table('sessions')
                    ->where('user_id', $target->id)
                    ->delete();

                // Remove Spatie associations
                $target->roles()->detach();
                $target->permissions()->detach();

                $target->delete();
            }
        );

        return response()->json([
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * Format safe user details.
     */
    private function formatUser(User $user): array
    {
        $user->loadMissing([
            'roles.permissions',
            'permissions',
        ]);

        $isSuperAdmin = $user->hasRole(
            'Super Admin',
            'web'
        );

        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'contact_no' => $user->contact_no,

            'roles' => $user->roles
                ->map(fn(Role $role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                ])
                ->values()
                ->all(),

            'direct_permissions' => $user->permissions
                ->pluck('name')
                ->sort()
                ->values()
                ->all(),

            'effective_permissions' => $isSuperAdmin
                ? ['*']
                : $user->getAllPermissions()
                ->pluck('name')
                ->sort()
                ->values()
                ->all(),

            'is_super_admin' => $isSuperAdmin,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
