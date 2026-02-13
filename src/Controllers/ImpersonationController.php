<?php

namespace IndieSystems\PermissionsAdminlteUi\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonationController extends Controller
{
    public function start(User $user, Request $request)
    {
        $admin = $request->user();

        if ($request->session()->has('impersonate_original_id')) {
            return back()->withErrors(['impersonation' => __('You are already impersonating someone. Stop first.')]);
        }

        if ($admin->id === $user->id) {
            return back()->withErrors(['impersonation' => __('You cannot impersonate yourself.')]);
        }

        // Store original admin ID in session
        $request->session()->put('impersonate_original_id', $admin->id);

        Log::info('Impersonation started', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'target_id' => $user->id,
            'target_email' => $user->email,
            'ip' => $request->ip(),
        ]);

        Auth::login($user);

        return redirect(config('permissions-ui.impersonation.redirect', '/'))
            ->withSuccess(__('Now impersonating :name.', ['name' => $user->name]));
    }

    public function stop(Request $request)
    {
        $originalId = $request->session()->get('impersonate_original_id');

        if (!$originalId) {
            return redirect('/');
        }

        $admin = User::findOrFail($originalId);

        $request->session()->forget('impersonate_original_id');

        Log::info('Impersonation stopped', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'was_impersonating_id' => $request->user()?->id,
            'ip' => $request->ip(),
        ]);

        Auth::login($admin);

        return redirect()->route('users.index')
            ->withSuccess(__('Impersonation ended. Welcome back, :name.', ['name' => $admin->name]));
    }
}
