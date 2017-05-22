<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Role comes before User seeder here.
        $this->call(RoleTableSeeder::class);
        // User seeder will use the roles above created.
        $this->call(UserTableSeeder::class);
        $this->call(UserTypeSeeder::class);
        $this->call(ScreenSeeder::class);
        $this->call(FormTemplateSeeder::class);
        $this->call(EmailTemplates::class);
        $this->call(ApprovalsSeeder::class);
        $this->call(StepsSeeder::class);
        $this->call(UsesEmailsSeeder::class);
    }
}
