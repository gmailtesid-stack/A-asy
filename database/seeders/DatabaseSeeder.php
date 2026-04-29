<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Outlets ───────────────────────────────────────────────
        $outletA = Outlet::create([
            'name'      => 'Outlet Pusat Jakarta',
            'code'      => 'OTL-JKT',
            'address'   => 'Jl. Sudirman No. 1, Jakarta Pusat',
            'phone'     => '021-12345678',
            'city'      => 'Jakarta',
            'is_active' => true,
        ]);

        $outletB = Outlet::create([
            'name'      => 'Outlet Bandung',
            'code'      => 'OTL-BDG',
            'address'   => 'Jl. Asia Afrika No. 5, Bandung',
            'phone'     => '022-98765432',
            'city'      => 'Bandung',
            'is_active' => true,
        ]);

        // ─── 2. Users ─────────────────────────────────────────────────
        User::create([
            'outlet_id' => null,
            'name'      => 'Super Admin',
            'email'     => 'admin@easy-pos.id',
            'password'  => Hash::make('password'),
            'role'      => 'super_admin',
            'is_active' => true,
        ]);

        User::create([
            'outlet_id' => $outletA->id,
            'name'      => 'Manager Jakarta',
            'email'     => 'manager.jkt@easy-pos.id',
            'password'  => Hash::make('password'),
            'role'      => 'manager',
            'is_active' => true,
        ]);

        User::create([
            'outlet_id' => $outletA->id,
            'name'      => 'Kasir Jakarta',
            'email'     => 'kasir.jkt@easy-pos.id',
            'password'  => Hash::make('password'),
            'role'      => 'cashier',
            'is_active' => true,
        ]);

        User::create([
            'outlet_id' => $outletB->id,
            'name'      => 'Manager Bandung',
            'email'     => 'manager.bdg@easy-pos.id',
            'password'  => Hash::make('password'),
            'role'      => 'manager',
            'is_active' => true,
        ]);

        // ─── 3. Categories ────────────────────────────────────────────
        $catMakanan  = Category::create(['name' => 'Makanan',  'slug' => 'makanan']);
        $catMinuman  = Category::create(['name' => 'Minuman',  'slug' => 'minuman']);
        $catSnack    = Category::create(['name' => 'Snack',    'slug' => 'snack']);

        // ─── 4. Products ──────────────────────────────────────────────
        $products = [
            ['category_id' => $catMakanan->id,  'name' => 'Nasi Goreng',    'sku' => 'MKN-001', 'price' => 25000, 'cost_price' => 12000],
            ['category_id' => $catMakanan->id,  'name' => 'Mie Ayam',       'sku' => 'MKN-002', 'price' => 20000, 'cost_price' => 10000],
            ['category_id' => $catMinuman->id,  'name' => 'Es Teh Manis',   'sku' => 'MNM-001', 'price' => 8000,  'cost_price' => 3000],
            ['category_id' => $catMinuman->id,  'name' => 'Jus Alpukat',    'sku' => 'MNM-002', 'price' => 18000, 'cost_price' => 8000],
            ['category_id' => $catSnack->id,    'name' => 'Kentang Goreng', 'sku' => 'SNK-001', 'price' => 15000, 'cost_price' => 6000],
            ['category_id' => $catSnack->id,    'name' => 'Pisang Goreng',  'sku' => 'SNK-002', 'price' => 12000, 'cost_price' => 5000],
        ];

        $createdProducts = [];
        foreach ($products as $p) {
            $createdProducts[] = Product::create(array_merge($p, ['unit' => 'porsi', 'is_active' => true]));
        }

        // ─── 5. Inventories (stok per outlet) ────────────────────────
        foreach ($createdProducts as $product) {
            Inventory::create([
                'outlet_id'    => $outletA->id,
                'product_id'   => $product->id,
                'quantity'     => rand(20, 100),
                'min_quantity' => 5,
            ]);

            Inventory::create([
                'outlet_id'    => $outletB->id,
                'product_id'   => $product->id,
                'quantity'     => rand(10, 60),
                'min_quantity' => 5,
            ]);
        }

        $this->command->info('✅ Database seeded: 2 outlets, 4 users, 6 products, 12 inventories.');
    }
}
