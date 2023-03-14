<?php

use Illuminate\Support\Facades\Route;
use IndieSystems\PermissionsAdminlteUi\Controllers\PermissionsController;
use IndieSystems\PermissionsAdminlteUi\Controllers\RolesController;
use IndieSystems\PermissionsAdminlteUi\Controllers\UserController;

Route::resource('users', UserController::class);
Route::resource('roles', RolesController::class);
Route::resource('permissions', PermissionsController::class);
