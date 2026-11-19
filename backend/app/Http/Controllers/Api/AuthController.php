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
     *
     * Authenticate using username and password.
     */
    public function login(LoginRequest $request): JsonResponse
    {
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

        // Prevent session fixation attacks
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
     * Return the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->formatUser(
                $request->user()
            ),
        ]);
    }

    /**
     * POST /api/logout
     *
     * Log out and invalidate the session.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    /**
     * Consistent safe user response.
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'contact_no' => $user->contact_no,
            'roles' => $user->getRoleNames()->values(),
            'is_super_admin' => $user->hasRole(
                'Super Admin',
                'web'
            ),
        ];
    }
}
