<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Permissions
        $permissions = [
            'view tenants',
            'create tenants',
            'edit tenants',
            'delete tenants',
            'manage settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Super Admin Role and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions($permissions);

        $tenantAdminRole = Role::firstOrCreate(['name' => 'Tenant Admin']);
        $tenantAdminRole->syncPermissions(['view tenants', 'edit tenants']);

        // 3. Create a Default Super Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@eios.vn'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123')
            ]
        );

        // Assign role to user
        if (!$admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }
    }
}
