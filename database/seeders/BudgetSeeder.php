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
            'IT'  => 850000000,
            'MED' => 2500000000,
            'HR'  => 300000000,
            'FIN' => 200000000,
        ];

        foreach ($budgets as $code => $amount) {
            $dept = Department::where('code', $code)->first();

            if ($dept) {
                Budget::updateOrCreate(
                    [
                        'department_id' => $dept->id,
                        'fiscal_year' => 2026
                    ],
                    [
                        'total_amount' => $amount,
                        'used_amount' => 0,
                        'reserved_amount' => 0
                    ]
                );
            }
        }
    }
}
