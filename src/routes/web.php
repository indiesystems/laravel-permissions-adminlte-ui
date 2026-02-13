<?php

use Illuminate\Support\Facades\Route;
use IndieSystems\PermissionsAdminlteUi\Controllers\ImpersonationController;
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
    Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::resource('users', UserController::class);
}

if ($config['roles'] ?? false) {
    Route::post('roles/bulk-action', [RolesController::class, 'bulkAction'])->name('roles.bulk-action');
    Route::post('roles/{role}/clone', [RolesController::class, 'clone'])->name('roles.clone');
    Route::resource('roles', RolesController::class);
}

if ($config['permissions'] ?? false) {
    Route::post('permissions/bulk-action', [PermissionsController::class, 'bulkAction'])->name('permissions.bulk-action');
    Route::resource('permissions', PermissionsController::class);
}

// Impersonation routes
if (config('permissions-ui.features.impersonation', true)) {
    Route::post('impersonate/{user}/start', [ImpersonationController::class, 'start'])
        ->name('users.impersonate.start')
        ->middleware('permission:' . config('permissions-ui.impersonation.permission', 'users.impersonate'));

    // Stop route has no permission check - impersonated user may not have admin perms
    Route::post('impersonate/stop', [ImpersonationController::class, 'stop'])
        ->name('users.impersonate.stop');
}
