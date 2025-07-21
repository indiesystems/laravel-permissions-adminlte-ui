<?php

use Illuminate\Support\Facades\Route;
use IndieSystems\PermissionsAdminlteUi\Controllers\PermissionsController;
use IndieSystems\PermissionsAdminlteUi\Controllers\RolesController;
use IndieSystems\PermissionsAdminlteUi\Controllers\UserController;

$config = config('permissions-ui.routes');

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

if ($config['manual_actions'] ?? false) {
    Route::post('users/manual-reset-password/{user}', [UserController::class,'resetPassword'])->name('users.manual-reset-password');
    Route::post('users/manual-resend-verification/{user}', [UserController::class,'resendVerification'])->name('users.manual-resend-verification');
}

if ($config['users'] ?? false) {
    Route::resource('users', UserController::class);
}

if ($config['roles'] ?? false) {
    Route::resource('roles', RolesController::class);
}

if ($config['permissions'] ?? false) {
    Route::resource('permissions', PermissionsController::class);
}
