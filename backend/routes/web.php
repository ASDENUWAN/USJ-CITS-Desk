
<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/**
 * Session-based authentication endpoints.
 *
 * Routes in web.php receive the web middleware,
 * including sessions and CSRF protection.
 */
Route::prefix('api')->group(function () {

    // Public login endpoint
    Route::post('/login', [
        AuthController::class,
        'login',
    ])->middleware('throttle:login');

    // Authenticated endpoints
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/me', [
            AuthController::class,
            'me',
        ]);

        Route::post('/logout', [
            AuthController::class,
            'logout',
        ]);
    });
});
