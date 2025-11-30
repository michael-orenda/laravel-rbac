<?php

use Illuminate\Support\Facades\Route;
use MichaelOrenda\Rbac\Http\Controllers\RoleController;
use MichaelOrenda\Rbac\Http\Controllers\PermissionController;
use MichaelOrenda\Rbac\Http\Controllers\ModelRoleController;
use MichaelOrenda\Rbac\Http\Controllers\ModelPermissionController;

Route::prefix('rbac')->group(function () {

    // Roles CRUD
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

    // Role permission assignment
    Route::post('/roles/{id}/permissions', [RoleController::class, 'attachPermissions']);
    Route::delete('/roles/{roleId}/permissions/{permissionId}', [RoleController::class, 'detachPermission']);
    Route::get('/roles/{id}/models', [RoleController::class, 'assignedModels']);

    // Permissions CRUD
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::post('/permissions', [PermissionController::class, 'store']);
    Route::get('/permissions/{id}', [PermissionController::class, 'show']);
    Route::put('/permissions/{id}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy']);
    Route::get('/permissions/{id}/models', [PermissionController::class, 'assignedModels']);

    // Polymorphic Role Assignment
    Route::get('/models/{type}/{id}/roles', [ModelRoleController::class, 'listRoles']);
    Route::post('/models/{type}/{id}/roles', [ModelRoleController::class, 'assignRole']);
    Route::delete('/models/{type}/{id}/roles', [ModelRoleController::class, 'revokeRole']);

    // Polymorphic Permission Assignment
    Route::get('/models/{type}/{id}/permissions', [ModelPermissionController::class, 'listPermissions']);
    Route::post('/models/{type}/{id}/permissions', [ModelPermissionController::class, 'assignPermission']);
    Route::delete('/models/{type}/{id}/permissions', [ModelPermissionController::class, 'revokePermission']);
});
