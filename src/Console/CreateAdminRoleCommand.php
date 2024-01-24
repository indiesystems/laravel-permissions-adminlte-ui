<?php

namespace IndieSystems\PermissionsAdminlteUi\Console;

use Illuminate\Console\Command;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Contracts\Role as RoleContract;

class CreateAdminRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create-basic-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin and user roles and assign all permissions to them.';

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
        $permissionClass = app(PermissionContract::class);
        $roleClass       = app(RoleContract::class);
        $permissions     = [];
        $guards          = $permissionClass::pluck('guard_name')->merge($roleClass::pluck('guard_name'))->unique();
        foreach ($guards as $guard) {
            $permissions = array_merge($permissions, $permissionClass::pluck('name')->toArray());
        }

        $adminRole = $roleClass::create([
            'name' => 'admin',
            'guard' => 'web',
        ]);

        $adminRole->syncPermissions($permissions);

        $userPermissions = [
            'profile.edit',
            'profile.list',
        ];

        $userRole = $roleClass::create([
            'name' => 'user',
            'guard' => 'web',
        ]);
        $userRole->syncPermissions($userPermissions);

        $this->info('Basic roles added successfully.');
    }
}
