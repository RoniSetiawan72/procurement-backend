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
        $budgets = [
            [
                'department_id' => 1, // IT Department
                'fiscal_year'   => 2026,
                'total_amount'  => 850000000,  // Rp 850 Juta
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => 2, // Medical Services
                'fiscal_year'   => 2026,
                'total_amount'  => 2500000000, // Rp 2.5 Miliar
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => 4, // Human Relationship
                'fiscal_year'   => 2026,
                'total_amount'  => 300000000,  // Rp 300 Juta
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
            [
                'department_id' => 5, // Finance
                'fiscal_year'   => 2026,
                'total_amount'  => 200000000,  // Rp 200 Juta
                'used_amount'   => 0,
                'reserved_amount' => 0,
            ],
        ];

        foreach ($budgets as $budget) {
            Budget::updateOrCreate(
                [
                    'department_id' => $budget['department_id'],
                    'fiscal_year' => $budget['fiscal_year']
                ],
                $budget
            );
        }
    }
}
