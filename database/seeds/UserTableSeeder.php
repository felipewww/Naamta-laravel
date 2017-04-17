<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin = Role::where('name', 'admin')->first();
        $role_staff = Role::where('name', 'staff')->first();
        $role_client = Role::where('name', 'client')->first();
        
        $admin = new User();
        $admin->name = 'Administrator';
        $admin->email = 'admin@blanko.be';
        $admin->password = bcrypt('123456');
        $admin->save();
        $admin->roles()->attach($role_admin);

        $staff = new User();
        $staff->name = 'Staff';
        $staff->email = 'staff@blanko.be';
        $staff->password = bcrypt('123456');
        $staff->save();
        $staff->roles()->attach($role_staff);

        $client = new User();
        $client->name = 'Client';
        $client->email = 'client@blanko.be';
        $client->password = bcrypt('123456');
        $client->save();
        $client->roles()->attach($role_client);
    }
}
