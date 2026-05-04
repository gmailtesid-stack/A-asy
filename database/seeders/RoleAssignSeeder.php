<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder untuk assign role ke user yang sudah ada di database.
 * Jalankan setelah RbacSeeder: php artisan db:seed --class=RoleAssignSeeder
 */
class RoleAssignSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil role yang sudah dibuat oleh RbacSeeder
        $adminRole      = \App\Models\Role::where('slug', 'admin')->first();
        $supervisorRole = \App\Models\Role::where('slug', 'supervisor')->first();
        $operatorRole   = \App\Models\Role::where('slug', 'operator')->first();

        if (!$adminRole) {
            $this->command->error('❌ Roles belum ada. Jalankan RbacSeeder terlebih dahulu.');
            return;
        }

        // Assign admin role ke Super Admin (email: admin@easy-pos.id)
        $admin = \App\Models\User::where('email', 'admin@easy-pos.id')->first();
        if ($admin) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
            $this->command->info("✅ Admin role assigned to: {$admin->email}");
        }

        // Assign supervisor role ke manager
        $managers = \App\Models\User::where('role', 'manager')->get();
        foreach ($managers as $manager) {
            if ($supervisorRole) {
                $manager->roles()->syncWithoutDetaching([$supervisorRole->id]);
                $this->command->info("✅ Supervisor role assigned to: {$manager->email}");
            }
        }

        // Assign operator role ke kasir
        $cashiers = \App\Models\User::where('role', 'cashier')->get();
        foreach ($cashiers as $cashier) {
            if ($operatorRole) {
                $cashier->roles()->syncWithoutDetaching([$operatorRole->id]);
                $this->command->info("✅ Operator role assigned to: {$cashier->email}");
            }
        }

        $this->command->info('🎉 Role assignment selesai!');
    }
}
