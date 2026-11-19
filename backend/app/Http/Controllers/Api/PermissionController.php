<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Support\RolePermissionGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * GET /api/permissions
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->paginate(50);

        return response()->json([
            'data' => $permissions->getCollection()
                ->map(fn(Permission $permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ]),

            'meta' => [
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
                'total' => $permissions->total(),
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
            'message' => 'Permission created successfully.',
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

            $guard->assertPermissionDeletable(
                $lockedPermission
            );

            $lockedPermission->delete();
        });

        return response()->json([
            'message' => 'Permission deleted successfully.',
        ]);
    }
}
