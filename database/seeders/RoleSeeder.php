<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create([ 'name' => 'admin' ]);
        $adminPermission = Permission::create(['name' => 'admin-access']);
        $adminRole->givePermissionTo($adminPermission);

        $userRole = Role::create([ 'name' => 'user' ]);
        $userPermission = Permission::create(['name' => 'user-access']);
        $userRole->givePermissionTo($userPermission);
        
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('aaaaa1!A')
        ]);

        $admin->assignRole('admin');
    }
}
