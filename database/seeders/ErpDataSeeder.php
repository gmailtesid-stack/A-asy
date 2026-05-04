<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Attendance;
use App\Models\Outlet;

class ErpDataSeeder extends Seeder
{
    public function run(): void
    {
        $outlet = Outlet::first();

        // 1. Seed Employees
        $employees = [
            [
                'employee_id' => 'EMP001',
                'name'        => 'Ahmad ERP Specialist',
                'position'    => 'Finance Manager',
                'salary'      => 15000000,
                'joined_at'   => '2024-01-01',
                'status'      => 'active'
            ],
            [
                'employee_id' => 'EMP002',
                'name'        => 'Siti Warehouse Lead',
                'position'    => 'Warehouse Supervisor',
                'salary'      => 8000000,
                'joined_at'   => '2024-02-15',
                'status'      => 'active'
            ],
        ];

        foreach ($employees as $emp) {
            $employee = Employee::create($emp);
            
            // 2. Seed Attendances for this month
            for ($i = 1; $i <= 5; $i++) {
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date'        => now()->subDays($i)->format('Y-m-d'),
                    'clock_in'    => '08:00:00',
                    'clock_out'   => '17:00:00',
                    'status'      => 'present'
                ]);
            }
        }

        // 3. Seed Customers
        $customers = [
            ['name' => 'Budi Pelanggan Setia', 'email' => 'budi@example.com', 'phone' => '08123456789', 'outlet_id' => $outlet->id, 'loyalty_points' => 500],
            ['name' => 'Ani New Member', 'email' => 'ani@example.com', 'phone' => '08987654321', 'outlet_id' => $outlet->id, 'loyalty_points' => 100],
        ];

        foreach ($customers as $cust) {
            Customer::create($cust);
        }
    }
}
