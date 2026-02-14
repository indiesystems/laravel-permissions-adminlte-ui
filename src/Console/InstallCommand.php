<?php

namespace IndieSystems\PermissionsAdminlteUi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class InstallCommand extends Command
{
    protected $signature = 'permissions-ui:install
                            {--no-migration : Skip running migrations}
                            {--no-seed : Skip seeding admin role and permissions}';

    protected $description = 'Install the Permissions AdminLTE UI package';

    public function handle(): int
    {
        $this->info('Installing Permissions AdminLTE UI...');
        $this->newLine();

        $this->checkDependencies();
        $this->publishConfig();

        if (!$this->option('no-migration')) {
            $this->runMigrations();
        }

        if (!$this->option('no-seed')) {
            $this->seedAdminRole();
        }

        $this->checkUserModel();
        $this->showPostInstall();

        $this->newLine();
        $this->info('Permissions UI installed successfully!');

        return self::SUCCESS;
    }

    protected function checkDependencies(): void
    {
        $this->components->task('Checking spatie/laravel-permission', function () {
            if (!class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                $this->newLine();
                $this->error('spatie/laravel-permission is required but not installed.');
                $this->line('  Run: composer require spatie/laravel-permission');
                return false;
            }
            return true;
        });

        $this->components->task('Checking permission tables exist', function () {
            return Schema::hasTable('roles') && Schema::hasTable('permissions');
        });
    }

    protected function publishConfig(): void
    {
        $this->components->task('Publishing config', function () {
            $this->callSilently('vendor:publish', [
                '--tag' => 'config',
                '--provider' => 'IndieSystems\PermissionsAdminlteUi\Providers\PermissionsUiProvider',
            ]);
            return true;
        });
    }

    protected function runMigrations(): void
    {
        if (Schema::hasColumn('users', 'status')) {
            $this->components->task('Status column already exists', fn () => true);
        } else {
            $this->components->task('Publishing and running migrations', function () {
                $this->callSilently('vendor:publish', [
                    '--tag' => 'permissions-ui-migrations',
                    '--provider' => 'IndieSystems\PermissionsAdminlteUi\Providers\PermissionsUiProvider',
                ]);
                $this->callSilently('migrate');
                return true;
            });
        }
    }

    protected function seedAdminRole(): void
    {
        $this->components->task('Creating admin role with all permissions', function () {
            $this->callSilently('permissions:create-admin-role');
            return true;
        });

        $this->components->task('Generating route permissions', function () {
            $this->callSilently('permissions:create-route-permissions');
            return true;
        });
    }

    protected function checkUserModel(): void
    {
        if (!class_exists('App\\Models\\User')) {
            return;
        }

        $traits = class_uses_recursive('App\\Models\\User');

        $this->components->task('User model has HasRoles trait', function () use ($traits) {
            return in_array('Spatie\\Permission\\Traits\\HasRoles', $traits);
        });

        if (!in_array('Spatie\\Permission\\Traits\\HasRoles', $traits)) {
            $this->warn('  Add to your User model:');
            $this->line('    use Spatie\\Permission\\Traits\\HasRoles;');
            $this->line('    class User extends Authenticatable { use HasRoles; }');
        }
    }

    protected function showPostInstall(): void
    {
        $this->newLine();
        $this->components->info('Post-install:');
        $this->newLine();

        $this->line('  1. Add the navigation to your sidebar:');
        $this->line("     @include('permissionsUi::layouts.navigation')");
        $this->newLine();

        $this->line('  2. (Optional) Assign admin role to a user:');
        $this->line('     php artisan permissions:assign-admin admin@example.com');
        $this->newLine();

        $this->line('  3. (Optional) Enable user status feature in config/permissions-ui.php:');
        $this->line("     'features' => ['user_status' => true]");
        $this->newLine();

        $this->line('  4. Visit /users, /roles, /permissions to manage.');
    }
}
