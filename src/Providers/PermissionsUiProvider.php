<?php

namespace IndieSystems\PermissionsAdminlteUi\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use IndieSystems\PermissionsAdminlteUi\Console\CreateRoutePermissionsCommand;
use IndieSystems\PermissionsAdminlteUi\Console\AssignAdminCommand;
use IndieSystems\PermissionsAdminlteUi\Console\CreateAdminRoleCommand;

class PermissionsUiProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadViewsFrom(__DIR__ . '/../views/', 'permissionsUi');
        $this->registerRoutes();
        // $this->loadMigrationsFrom();
        // seeder?
        // $this->publishes([
        //     __DIR__.'/../config/permissionUI.php' => config_path('permissionUI.php'),
        // ]);
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateRoutePermissionsCommand::class,
                AssignAdminCommand::class,
                CreateAdminRoleCommand::class,
            ]);
        }
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
            'middleware' => ['web','auth'],
        ];
    }
}
