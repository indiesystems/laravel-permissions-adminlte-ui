<?php

namespace IndieSystems\PermissionsAdminlteUi\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permissions.index|permissions.store|permissions.show|permissions.create|permissions.edit|permissions.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:permissions.create', ['only' => ['create', 'store', 'sync']]);
        $this->middleware('permission:permissions.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:permissions.delete', ['only' => ['destroy', 'bulkAction']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::all();

        return view('permissionsUi::permissions.index', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Show form for creating permissions
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('permissionsUi::permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create($request->only('name'));

        return redirect()->route('permissions.index')
            ->withSuccess(__('Permission created successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Permission  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        return view('permissionsUi::permissions.edit', [
            'permission' => $permission
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,'.$permission->id
        ]);

        $permission->update($request->only('name'));

        return redirect()->route('permissions.index')
            ->withSuccess(__('Permission updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')
            ->withSuccess(__('Permission deleted successfully.'));
    }

    /**
     * Sync permissions from routes.
     */
    public function sync()
    {
        $before = Permission::count();

        Artisan::call('permission:create-route-permissions');

        $after = Permission::count();
        $created = $after - $before;

        if ($created > 0) {
            return redirect()->route('permissions.index')
                ->withSuccess(__(':count new permission(s) created from routes.', ['count' => $created]));
        }

        return redirect()->route('permissions.index')
            ->withSuccess(__('Permissions are up to date. No new routes found.'));
    }

    /**
     * Bulk actions on permissions.
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
            Permission::whereIn('id', $ids)->delete();
            return back()->withSuccess(__(':count permission(s) deleted.', ['count' => count($ids)]));
        }

        return back();
    }
}
