<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itDept = Department::where('code', 'IT')->first();
        $medDept = Department::where('code', 'MED')->first();
        $hrDept = Department::where('code', 'HR')->first();
        $finDept = Department::where('code', 'FIN')->first();

        $budgets = [
            [
                'department_id' => $itDept->id ?? 1,
                'fiscal_year'   => 2026,
                'total_amount'  => 750000000,
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => $medDept->id ?? 2,
                'fiscal_year'   => 2026,
                'total_amount'  => 1500000000,
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => $hrDept->id ?? 4,
                'fiscal_year'   => 2026,
                'total_amount'  => 200000000,
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => $finDept->id ?? 5,
                'fiscal_year'   => 2026,
                'total_amount'  => 150000000,
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => $itDept->id ?? 1,
                'fiscal_year'   => 2025,
                'total_amount'  => 500000000,
                'used_amount'   => 450000000,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => $medDept->id ?? 2,
                'fiscal_year'   => 2025,
                'total_amount'  => 1200000000,
                'used_amount'   => 1100000000,
                'reserved_amount' => 0,
            ],
            // Tambahan Departemen lain jika ada
            [
                'department_id' => 5, // Asumsi ID 5
                'fiscal_year'   => 2026,
                'total_amount'  => 300000000,
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => 4,
                'fiscal_year'   => 2026,
                'total_amount'  => 100000000,
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => 2,
                'fiscal_year'   => 2026,
                'total_amount'  => 450000000,
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => 1,
                'fiscal_year'   => 2026,
                'total_amount'  => 600000000,
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
        ];

        foreach ($budgets as $budget) {
            Budget::updateOrCreate(
                ['department_id' => $budget['department_id'], 'fiscal_year' => $budget['fiscal_year']],
                $budget
            );
        }
    }
}
