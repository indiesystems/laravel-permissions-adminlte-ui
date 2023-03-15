<?php

namespace IndieSystems\PermissionsAdminlteUi\Console;

use App\Models\User;
use Illuminate\Console\Command;

class AssignAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:assign-admin {user : user email}';

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
        $user = User::where('email', '=', $this->argument('user'))->firstOrFail();
        $user->syncRoles('admin');
        $this->info('Admin role assigned successfully.');
    }
}