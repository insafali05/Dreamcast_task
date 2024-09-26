<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define roles
        $roles = [
            ['name' => 'Admin'],
            ['name' => 'User'],
            ['name' => 'Manager'],
            ['name' => 'Editor'],
        ];

        // Insert roles into the database
        Role::insert($roles);
    }
}
