<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Support\RolePermissionGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * GET /api/permissions
     *
     * Supports:
     * ?search=tickets
     * ?page=2
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => [
                'sometimes',
                'string',
                'max:100',
            ],
        ]);

        $search = trim(
            $validated['search'] ?? ''
        );

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    // Escape SQL LIKE wildcards.
                    $term = addcslashes(
                        $search,
                        '%_\\'
                    );

                    $query->where(
                        'name',
                        'like',
                        "%{$term}%"
                    );
                }
            )
            ->orderBy('name')
            ->paginate(50);

        return response()->json([
            'data' => $permissions
                ->getCollection()
                ->map(fn(Permission $permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ])
                ->values(),

            'meta' => [
                'current_page' =>
                $permissions->currentPage(),

                'last_page' =>
                $permissions->lastPage(),

                'per_page' =>
                $permissions->perPage(),

                'total' =>
                $permissions->total(),
            ],
        ]);
    }

    /**
     * POST /api/permissions
     */
    public function store(
        StorePermissionRequest $request
    ): JsonResponse {
        $permission = Permission::create([
            'name' => $request->validated('name'),
            'guard_name' => 'web',
        ]);

        return response()->json([
            'message' =>
            'Permission created successfully.',

            'data' => [
                'id' => $permission->id,
                'name' => $permission->name,
            ],
        ], 201);
    }

    /**
     * DELETE /api/permissions/{permission}
     */
    public function destroy(
        Permission $permission,
        RolePermissionGuard $guard
    ): JsonResponse {
        DB::transaction(function () use (
            $permission,
            $guard
        ): void {
            $lockedPermission = Permission::query()
                ->whereKey($permission->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Protect core and assigned permissions.
            $guard->assertPermissionDeletable(
                $lockedPermission
            );

            $lockedPermission->delete();
        });

        return response()->json([
            'message' =>
            'Permission deleted successfully.',
        ]);
    }
}
