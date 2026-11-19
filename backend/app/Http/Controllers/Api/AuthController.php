<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/login
     */
    public function login(
        LoginRequest $request
    ): JsonResponse {
        $credentials = $request->validated();

        if (! Auth::guard('web')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ])) {
            throw ValidationException::withMessages([
                'username' => [
                    'Invalid username or password.',
                ],
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::guard('web')->user();

        return response()->json([
            'message' => 'Login successful.',
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * GET /api/me
     *
     * Always return current access information.
     */
    public function me(Request $request): JsonResponse
    {
        $user = User::query()
            ->with([
                'roles.permissions',
                'permissions',
            ])
            ->findOrFail($request->user()->id);

        return response()->json([
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * POST /api/logout
     */
    public function logout(
        Request $request
    ): JsonResponse {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    /**
     * Format authenticated user.
     *
     * Keeps role permissions and direct
     * permissions correctly separated.
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

        $directPermissions = $user
            ->getDirectPermissions()
            ->pluck('name')
            ->unique()
            ->sort()
            ->values()
            ->all();

        $inheritedPermissions = $user
            ->getPermissionsViaRoles()
            ->pluck('name')
            ->unique()
            ->sort()
            ->values()
            ->all();

        $effectivePermissions = $isSuperAdmin
            ? ['*']
            : $user->getAllPermissions()
            ->pluck('name')
            ->unique()
            ->sort()
            ->values()
            ->all();

        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'contact_no' => $user->contact_no,

            'roles' => $user
                ->getRoleNames()
                ->values()
                ->all(),

            'direct_permissions' => $directPermissions,

            'inherited_permissions' =>
            $inheritedPermissions,

            'permissions' => $effectivePermissions,

            'is_super_admin' => $isSuperAdmin,
        ];
    }
}
