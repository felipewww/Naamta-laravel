<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_employee = new Role();
        $role_employee->name = 'admin';
        $role_employee->description = 'A Admin User';
        $role_employee->save();
        $role_manager = new Role();
        $role_manager->name = 'staff';
        $role_manager->description = 'A Staff User';
        $role_manager->save();
        $role_manager = new Role();
        $role_manager->name = 'client';
        $role_manager->description = 'A Client User';
        $role_manager->save();
        $role_none = new Role();
        $role_none->name = 'none';
        $role_none->description = 'A not categorized User';
        $role_none->save();
    }
}
