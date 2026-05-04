<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets
            ['code' => '1101', 'name' => 'Kas POS', 'type' => 'asset'],
            ['code' => '1102', 'name' => 'Bank / QRIS', 'type' => 'asset'],
            ['code' => '1201', 'name' => 'Persediaan Barang', 'type' => 'asset'],
            
            // Liabilities
            ['code' => '2101', 'name' => 'Hutang Dagang', 'type' => 'liability'],
            ['code' => '2201', 'name' => 'PPN Keluaran', 'type' => 'liability'],
            
            // Equity
            ['code' => '3101', 'name' => 'Modal Pemilik', 'type' => 'equity'],
            
            // Revenues
            ['code' => '4101', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue'],
            ['code' => '4102', 'name' => 'Potongan Penjualan (Discount)', 'type' => 'revenue'],
            
            // Expenses
            ['code' => '5101', 'name' => 'Harga Pokok Penjualan (HPP)', 'type' => 'expense'],
            ['code' => '5201', 'name' => 'Biaya Operasional', 'type' => 'expense'],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(['code' => $account['code']], $account);
        }
    }
}
