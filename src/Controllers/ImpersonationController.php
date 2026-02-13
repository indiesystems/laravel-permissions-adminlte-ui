<?php

namespace IndieSystems\PermissionsAdminlteUi\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function start(User $user, Request $request)
    {
        $admin = $request->user();

        if ($admin->id === $user->id) {
            return back()->withErrors(['impersonation' => __('You cannot impersonate yourself.')]);
        }

        // Store original admin ID in session
        $request->session()->put('impersonate_original_id', $admin->id);

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

        Auth::login($admin);

        return redirect()->route('users.index')
            ->withSuccess(__('Impersonation ended. Welcome back, :name.', ['name' => $admin->name]));
    }
}
