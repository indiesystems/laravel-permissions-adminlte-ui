<?php

namespace IndieSystems\PermissionsAdminlteUi\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.list|roles.create|roles.edit|roles.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:roles.create', ['only' => ['create', 'store', 'clone']]);
        $this->middleware('permission:roles.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:roles.delete', ['only' => ['destroy', 'bulkAction']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->orderBy('id', 'DESC')->paginate();
        return view('permissionsUi::roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::get();
        return view('permissionsUi::roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'       => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->get('name')]);
        $role->syncPermissions($request->get('permission'));

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        $rolePermissions = $role->permissions;

        // Get users with this role
        $users = User::role($role->name)->with('roles')->paginate(20, ['*'], 'users_page');

        return view('permissionsUi::roles.show', compact('role', 'rolePermissions', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $role            = $role;
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions     = Permission::get();

        return view('permissionsUi::roles.edit', compact('role', 'rolePermissions', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Role $role, Request $request)
    {
        $this->validate($request, [
            'name'       => 'required|unique:roles,name,' . $role->id,
            'permission' => 'required',
        ]);

        $role->update($request->only('name'));

        $role->syncPermissions($request->get('permission'));

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->withErrors(['delete' => __('Cannot delete role ":name" — it still has :count user(s) assigned.', ['name' => $role->name, 'count' => $role->users()->count()])]);
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }

    /**
     * Clone a role with all its permissions.
     */
    public function clone(Role $role)
    {
        $newName = $role->name . '-copy';
        $counter = 1;
        while (Role::where('name', $newName)->exists()) {
            $newName = $role->name . '-copy-' . $counter++;
        }

        $newRole = DB::transaction(function () use ($role, $newName) {
            $newRole = Role::create([
                'name' => $newName,
                'guard_name' => $role->guard_name,
            ]);
            $newRole->syncPermissions($role->permissions);
            return $newRole;
        });

        return redirect()->route('roles.edit', $newRole->id)
            ->with('success', __('Role cloned as ":name". Rename it and adjust permissions.', ['name' => $newName]));
    }

    /**
     * Bulk actions on roles.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'action' => 'required|in:delete',
        ]);

        $ids = $request->get('ids');

        if ($request->get('action') === 'delete') {
            // Skip roles that still have users assigned
            $rolesWithUsers = Role::whereIn('id', $ids)->whereHas('users')->pluck('name');
            if ($rolesWithUsers->isNotEmpty()) {
                return back()->withErrors(['bulk' => __('Cannot delete roles with assigned users: :names', ['names' => $rolesWithUsers->join(', ')])]);
            }
            Role::whereIn('id', $ids)->delete();
            return back()->withSuccess(__(':count role(s) deleted.', ['count' => count($ids)]));
        }

        return back();
    }
}
