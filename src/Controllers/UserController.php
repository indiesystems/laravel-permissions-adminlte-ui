<?php

namespace IndieSystems\PermissionsAdminlteUi\Controllers;

use IndieSystems\PermissionsAdminlteUi\Requests\StoreUserRequest;
use IndieSystems\PermissionsAdminlteUi\Requests\UpdateUserRequest;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function __construct()
    {
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
        return view('permissionsUi::users.create');
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
        //For demo purposes only. When creating user or inviting a user
        // you should create a generated random password and email it to the user
        $user->create(array_merge($request->validated(), [
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]));

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

        $user->syncRoles($request->get('role'));

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
}
