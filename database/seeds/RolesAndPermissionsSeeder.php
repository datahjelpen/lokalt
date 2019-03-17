<?php

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        $role_superadmin = new Role;
        $role_superadmin->name = 'superadmin';
        $role_superadmin->save();

        $role_admin = new Role;
        $role_admin->name = 'admin';
        $role_admin->save();

        $role_user = new Role;
        $role_user->name = 'user';
        $role_user->save();
    }
}
