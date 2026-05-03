<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Permissions
        $permissionsList = [
            'view-dashboard',
            'manage-master-data',
            'view-master-data',
            'manage-stock-adjustment',
            'create-po',
            'confirm-po',
            'create-grn',
            'process-putaway',
            'create-so',
            'confirm-so',
            'process-picking',
            'process-packing',
            'process-shipping',
            'view-reports',
            'manage-users',
        ];

        foreach ($permissionsList as $p) {
            \App\Models\Permission::firstOrCreate([
                'slug' => $p,
                'name' => ucwords(str_replace('-', ' ', $p))
            ]);
        }

        // 2. Define Role Permissions
        $roles = [
            'admin' => $permissionsList, // All
            'supervisor' => array_diff($permissionsList, ['manage-users']),
            'operator' => [
                'view-dashboard',
                'view-master-data',
                'create-grn',
                'process-putaway',
                'create-so',
                'process-picking',
                'process-packing',
                'process-shipping',
                'view-reports',
            ],
        ];

        foreach ($roles as $roleSlug => $rolePermissions) {
            $role = \App\Models\Role::firstOrCreate([
                'slug' => $roleSlug,
                'name' => ucfirst($roleSlug),
                'description' => "Default $roleSlug role"
            ]);

            $permissionIds = \App\Models\Permission::whereIn('slug', $rolePermissions)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}
