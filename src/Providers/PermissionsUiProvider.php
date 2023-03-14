<?php

namespace IndieSystems\PermissionsAdminlteUi\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
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
        // $router->middlewareGroup('web', [PermissionMiddleware::class]);
        // $router->pushMiddlewareToGroup('web', [PermissionMiddleware::class]);

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
