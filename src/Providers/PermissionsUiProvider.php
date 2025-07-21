<?php
namespace IndieSystems\PermissionsAdminlteUi\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use IndieSystems\PermissionsAdminlteUi\Console\AssignAdminCommand;
use IndieSystems\PermissionsAdminlteUi\Console\CreateAdminRoleCommand;
use IndieSystems\PermissionsAdminlteUi\Console\CreateRoutePermissionsCommand;

class PermissionsUiProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        require_once __DIR__ . '/../helpers.php';
        $this->loadViewsFrom(__DIR__ . '/../views/', 'permissionsUi');
        $this->registerRoutes();
        // $this->loadMigrationsFrom();
        // seeder?
        $this->publishes([
            __DIR__ . '/../config/permissions-ui.php' => config_path('permissions-ui.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateRoutePermissionsCommand::class,
                AssignAdminCommand::class,
                CreateAdminRoleCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/permissions-ui.php', 'permissions-ui');
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'middleware' => ['web', 'auth'],
        ];
    }
}
