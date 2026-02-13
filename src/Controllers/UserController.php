<?php

namespace IndieSystems\PermissionsAdminlteUi\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use IndieSystems\PermissionsAdminlteUi\Notifications\NewUser;
use IndieSystems\PermissionsAdminlteUi\Notifications\UserPasswordReset;
use IndieSystems\PermissionsAdminlteUi\Requests\StoreUserRequest;
use IndieSystems\PermissionsAdminlteUi\Requests\UpdateUserRequest;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:users.list|users.create|users.edit|users.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:users.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:users.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:users.delete', ['only' => ['destroy', 'bulkAction']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Server-side search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->get('role')) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        // Filter by verified status
        if ($request->has('verified') && $request->get('verified') !== '') {
            if ($request->get('verified') === '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Filter by status (if feature enabled)
        if (config('permissions-ui.features.user_status') && ($status = $request->get('status'))) {
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate()->appends($request->query());
        $roles = Role::orderBy('name')->get();

        return view('permissionsUi::users.index', compact('users', 'roles'));
    }

    /**
     * Show form for creating user
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('permissionsUi::users.create', [
            'userRole' => ['user'],
            'roles' => Role::latest()->get(),
        ]);
    }

    /**
     * Store a newly created user
     *
     * @param User $user
     * @param StoreUserRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(User $user, StoreUserRequest $request)
    {
        $plainTextPassword = !empty($request->password) ? $request->password : Str::random(10);

        // Pass plain-text password — the User model's setPasswordAttribute mutator handles bcrypt
        $newUser = $user->create(array_merge($request->validated(), [
            'password' => $plainTextPassword,
        ]));

        $newUser->syncRoles($request->get('role') ? Role::find($request->get('role')) : 'user');

        Notification::sendNow($newUser, new NewUser($plainTextPassword));

        return redirect()->route('users.index')
            ->withSuccess(__('User created successfully.'));
    }

    /**
     * Show user data
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load('roles.permissions');

        // Get all permissions: direct + via roles
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->unique()->sort()->values();
        $directPermissions = $user->getDirectPermissions()->pluck('name')->unique()->sort()->values();

        return view('permissionsUi::users.show', [
            'user' => $user,
            'rolePermissions' => $rolePermissions,
            'directPermissions' => $directPermissions,
        ]);
    }

    /**
     * Edit user data
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('permissionsUi::users.edit', [
            'user'     => $user,
            'userRole' => $user->roles->pluck('name')->toArray(),
            'roles'    => Role::latest()->get(),
        ]);
    }

    /**
     * Update user data
     *
     * @param User $user
     * @param UpdateUserRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        $user->update($request->validated());

        // Handle status update if feature enabled (saved separately — host app may not have 'status' in $fillable)
        if (config('permissions-ui.features.user_status') && $request->has('status')) {
            $statuses = config('permissions-ui.user_statuses', ['active', 'suspended', 'banned']);
            if (in_array($request->get('status'), $statuses)) {
                $user->forceFill(['status' => $request->get('status')])->save();
            }
        }
        $user->syncRoles(Role::find($request->get('role')));

        return redirect()->route('users.index')
            ->withSuccess(__('User updated successfully.'));
    }

    /**
     * Delete user data
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Request $request)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['delete' => __('You cannot delete your own account.')]);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
    }

    /**
     * Bulk actions on users.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'action' => 'required|in:delete,assign_role,verify',
        ]);

        $ids = $request->get('ids');
        // Never include current user in bulk operations
        $ids = array_diff($ids, [$request->user()->id]);

        if (empty($ids)) {
            return back()->withErrors(['bulk' => __('No valid users selected.')]);
        }

        switch ($request->get('action')) {
            case 'delete':
                User::whereIn('id', $ids)->delete();
                return back()->withSuccess(__(':count user(s) deleted.', ['count' => count($ids)]));

            case 'assign_role':
                $request->validate(['bulk_role' => 'required|exists:roles,id']);
                $role = Role::findById($request->get('bulk_role'));
                $users = User::whereIn('id', $ids)->get();
                foreach ($users as $user) {
                    $user->assignRole($role);
                }
                return back()->withSuccess(__(':count user(s) assigned to role :role.', ['count' => count($ids), 'role' => $role->name]));

            case 'verify':
                User::whereIn('id', $ids)->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
                return back()->withSuccess(__('Selected users verified.'));
        }

        return back();
    }

    public function resendVerification(User $user, Request $request)
    {
        if (!($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail)) {
            return redirect()->route('users.index')
                ->withError(__('Users are not required to verify their emails.'));
        }

        $user->sendEmailVerificationNotification();

        return redirect()->route('users.index')
            ->withSuccess(__('Verification email sent to :email.', ['email' => $user->email]));
    }

    public function resetPassword(User $user, Request $request)
    {
        $plainTextPassword = Str::random(10);
        $user->password    = $plainTextPassword;
        $user->save();
        Notification::sendNow($user, new UserPasswordReset($plainTextPassword));
        return redirect()->route('users.index')
            ->withSuccess(__('User password reset and sent to email.'));
    }
}
