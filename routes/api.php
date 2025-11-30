<?php

use Illuminate\Support\Facades\Route;
use MichaelOrenda\Rbac\Http\Controllers\RoleController;

Route::get('rbac/roles', [RoleController::class, 'index']);
