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
        $admin->verified = true;
        $admin->save();
        $admin->roles()->attach($role_admin);

        $staff = new User();

        $staff->name = 'Staff';
        $staff->email = 'staff@blanko.be';
        $staff->password = bcrypt('123456');
        $staff->verified = true;
        $staff->save();
        $staff->roles()->attach($role_staff);

        $staff2 = new User();

        $staff2->name = 'Staff 2';
        $staff2->email = 'staff2@blanko.be';
        $staff2->password = bcrypt('123456');
        $staff2->verified = true;
        $staff2->save();
        $staff2->roles()->attach($role_staff);

        $staff3 = new User();

        $staff3->name = 'Staff 3';
        $staff3->email = 'staff3@blanko.be';
        $staff3->password = bcrypt('123456');
        $staff3->verified = true;
        $staff3->save();
        $staff3->roles()->attach($role_staff);

        $staff4 = new User();

        $staff4->name = 'Staff 4';
        $staff4->email = 'staff4@blanko.be';
        $staff4->password = bcrypt('123456');
        $staff4->verified = true;
        $staff4->save();
        $staff4->roles()->attach($role_staff);
    }
}
