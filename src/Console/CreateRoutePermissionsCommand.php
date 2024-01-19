<?php

namespace IndieSystems\PermissionsAdminlteUi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class CreateRoutePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create-route-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a route permissions.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = Route::getRoutes()->getRoutes();

        $permissionMap = [
            // perm => route name suuffix
            'list'   => ['index', 'show'],
            'create' => ['create', 'store'],
            'edit'   => ['edit', 'update'],
            'delete' => ['destroy'],
        ];

        $skip = ['sanctum', 'generated'];

        foreach ($routes as $route) {
            foreach ($skip as $key => $skipKeyword) {
                if (strpos($route->getName(), $skipKeyword) !== false) {
                    continue 2;
                }
            }
            if ($route->getName() != '' && isset($route->getAction()['middleware']) && 
                in_array('web', $route->getAction()['middleware'])) {

                $permissionName = null;

                foreach ($permissionMap as $name => $suffixes) {
                    $routeNameSegments     = explode('.', $route->getName());
                    $routeSuffix           = array_pop($routeNameSegments);
                    $routePermissionPrefix = implode('.',$routeNameSegments);
                    if (in_array($routeSuffix, $suffixes)) {
                        $permissionName = $routePermissionPrefix . '.' . $name;
                    }
                }

                if (is_null($permissionName)) {
                    $permissionName = $route->getName();
                }

                $permission = Permission::where('name', $permissionName)->first();

                if (is_null($permission)) {
                    Permission::create(['name' => $permissionName]);
                }
            }
        }

        $this->info('Route permissions added successfully.');
    }
}
