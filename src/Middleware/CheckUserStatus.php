<?php

namespace IndieSystems\PermissionsAdminlteUi\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('permissions-ui.features.user_status', false)) {
            return $next($request);
        }

        $user = $request->user();

        // Skip check during impersonation — admin intentionally chose this user
        if ($request->session()->has('impersonate_original_id')) {
            return $next($request);
        }

        if ($user && isset($user->status) && $user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['status' => __('Your account is :status.', ['status' => $user->status])]);
        }

        return $next($request);
    }
}
