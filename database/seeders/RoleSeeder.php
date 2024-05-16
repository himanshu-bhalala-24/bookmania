<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create([ 'name' => 'admin' ]);
        $userRole = Role::create([ 'name' => 'user' ]);

        User::create([
            'role_id' => $adminRole->id,
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('aaaaa1!A')
        ]);
    }
}
