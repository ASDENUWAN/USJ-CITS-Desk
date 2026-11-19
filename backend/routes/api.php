
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;

Route::middleware('auth:sanctum')->group(function () {

    // ROLE MANAGEMENT

    Route::get('/roles', [
        RoleController::class,
        'index'
    ])->middleware('can:roles.view');

    Route::post('/roles', [
        RoleController::class,
        'store'
    ])->middleware('can:roles.create');

    Route::get('/roles/{role}', [
        RoleController::class,
        'show'
    ])->middleware('can:roles.view');

    Route::match(['put', 'patch'], '/roles/{role}', [
        RoleController::class,
        'update'
    ])->middleware('can:roles.update');

    Route::put('/roles/{role}/permissions', [
        RoleController::class,
        'syncPermissions'
    ])->middleware([
        'can:roles.update',
        'can:permissions.assign',
    ]);

    Route::delete('/roles/{role}', [
        RoleController::class,
        'destroy'
    ])->middleware('can:roles.delete');


    // PERMISSION MANAGEMENT

    Route::get('/permissions', [
        PermissionController::class,
        'index'
    ])->middleware('can:permissions.view');

    Route::post('/permissions', [
        PermissionController::class,
        'store'
    ])->middleware('can:permissions.create');

    Route::delete('/permissions/{permission}', [
        PermissionController::class,
        'destroy'
    ])->middleware('can:permissions.delete');
});


/*
|--------------------------------------------------------------------------
| User Management and Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // USER MANAGEMENT

    Route::get('/users', [
        UserController::class,
        'index'
    ])->middleware('can:users.view');

    Route::post('/users', [
        UserController::class,
        'store'
    ])->middleware('can:users.create');

    Route::get('/users/{user}', [
        UserController::class,
        'show'
    ])->middleware('can:users.view');

    Route::patch('/users/{user}', [
        UserController::class,
        'update'
    ])->middleware('can:users.update');

    Route::delete('/users/{user}', [
        UserController::class,
        'destroy'
    ])->middleware('can:users.delete');

    // ROLE ASSIGNMENT

    Route::put('/users/{user}/roles', [
        UserController::class,
        'syncRoles'
    ])->middleware([
        'can:users.update',
        'can:roles.assign',
    ]);

    // DIRECT PERMISSION ASSIGNMENT

    Route::put('/users/{user}/permissions', [
        UserController::class,
        'syncPermissions'
    ])->middleware([
        'can:users.update',
        'can:permissions.assign',
    ]);

    // PERSONAL PROFILE

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ]);
});
