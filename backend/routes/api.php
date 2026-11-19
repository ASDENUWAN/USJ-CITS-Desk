
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;

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
