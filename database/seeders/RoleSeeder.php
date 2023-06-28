<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrator',
            ],
            [
                'name' => 'user',
                'description' => 'User',
            ],
            [
                'name' => 'writer',
                'description' => 'Writer',
            ],
            [
                'name' => 'accountant',
                'description' => 'Accountant',
            ],
            [
                'name' => 'ceo',
                'description' => 'Chief executive officer',
            ],
            [
                'name' => 'cso',
                'description' => 'Chief strategy officer',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        //create admin user
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123123123'),
            'email_verified_at' => now(),
        ]);
        $user->roles()->attach(Role::where('name', 'admin')->first());
    }
}
