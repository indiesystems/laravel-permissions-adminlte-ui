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
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:users.list|users.create|users.edit|users.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:users.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:users.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:users.delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles')->paginate();
        return view('permissionsUi::users.index', compact('users'));
    }

    /**
     * Show form for creating user
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('permissionsUi::users.create', [
            'userRole' => Role::findByName('user')->toArray(),
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
        if (isset($request->password) || !empty($request->password)) {
            $plainTextPassword = $request->password;
        } else {
            $plainTextPassword = Str::random(10);
        }

        $password = bcrypt($plainTextPassword);

        $newUser = $user->create(array_merge($request->validated(), [
            'password' => $password,
        ]));

        $newUser->syncRoles(Role::find($request->only('role')) ?: 'user');

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
        return view('permissionsUi::users.show', [
            'user' => $user,
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
        // dd($user->roles->pluck('name')->toArray());
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
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
    }

    public function resendVerification(User $user, Request $request)
    {
        if (class_implements($user, 'MustVerifyEmail')) {
            $user->sendEmailVerificationNotification();
            return redirect()->route('users.index')
                ->withSuccess(__('Verification send successfully.'));
        } else {
            return redirect()->route('users.index')
                ->withError(__('Users are not required to verify their emails.'));
        }
    }

    public function resetPassword(User $user, Request $request)
    {
        $plainTextPassword = Str::random(10);
        $user->password    = $plainTextPassword;
        $user->save();
        Notification::sendNow($user, new UserPasswordReset($plainTextPassword));
        return redirect()->route('users.index')
            ->withError(__('User password reset and send to email.'));
    }
}
