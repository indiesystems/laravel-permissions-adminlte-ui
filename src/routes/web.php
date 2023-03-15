<?php

use Illuminate\Support\Facades\Route;
use IndieSystems\PermissionsAdminlteUi\Controllers\PermissionsController;
use IndieSystems\PermissionsAdminlteUi\Controllers\RolesController;
use IndieSystems\PermissionsAdminlteUi\Controllers\UserController;

Auth::routes(['verify' => true]);
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::resource('users', UserController::class);
Route::resource('roles', RolesController::class);
Route::resource('permissions', PermissionsController::class);
