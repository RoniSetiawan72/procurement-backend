<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Department;
use App\Models\PurchaseRequisition;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseRequisitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = User::first()->id ?? 1;

        $itId = Department::where('code', 'IT')->first()->id ?? 1;
        $medId = Department::where('code', 'MED')->first()->id ?? 2;
        $hrId = Department::where('code', 'HR')->first()->id ?? 3;
        $finId = Department::where('code', 'FIN')->first()->id ?? 4;

        $dummyPRs = [
            [
                'department_id' => 1,
                'title' => 'Pengadaan Monitor Eksternal 24 Inch',
                'description' => 'Untuk tim developer agar lebih produktif.',
                'status' => 'draft',
                'items' => [
                    ['item_name' => 'Monitor LED 24 Inch', 'quantity' => 5, 'uom' => 'Unit', 'estimated_unit_price' => 2500000],
                    ['item_name' => 'Kabel HDMI 2 Meter', 'quantity' => 5, 'uom' => 'Pcs', 'estimated_unit_price' => 75000],
                ]
            ],
            [
                'department_id' => 1,
                'title' => 'Lisensi Software Antivirus Tahunan',
                'description' => 'Perpanjangan lisensi Kaspersky untuk 50 PC.',
                'status' => 'submitted',
                'items' => [
                    ['item_name' => 'Antivirus Enterprise License', 'quantity' => 50, 'uom' => 'License', 'estimated_unit_price' => 350000],
                ]
            ],
            [
                'department_id' => 1,
                'title' => 'Upgrade Server RAM & SSD',
                'description' => 'Kebutuhan database server utama.',
                'status' => 'approved',
                'items' => [
                    ['item_name' => 'RAM Server 64GB DDR4', 'quantity' => 2, 'uom' => 'Pcs', 'estimated_unit_price' => 8000000],
                    ['item_name' => 'SSD Enterprise 2TB', 'quantity' => 4, 'uom' => 'Pcs', 'estimated_unit_price' => 4500000],
                ]
            ],

            // --- DEPARTMENT 2 (MEDICAL SERVICES) ---
            [
                'department_id' => 2,
                'title' => 'Alat Suntik & Kasa Steril IGD',
                'description' => 'Restock perlengkapan harian IGD bulan depan.',
                'status' => 'draft',
                'items' => [
                    ['item_name' => 'Syringe 5ml', 'quantity' => 100, 'uom' => 'Box', 'estimated_unit_price' => 125000],
                    ['item_name' => 'Kasa Steril 16x16', 'quantity' => 50, 'uom' => 'Pack', 'estimated_unit_price' => 85000],
                ]
            ],
            [
                'department_id' => 2,
                'title' => 'Pengadaan Kursi Roda Tambahan',
                'description' => 'Menambah 5 kursi roda untuk area lobi utama.',
                'status' => 'submitted',
                'items' => [
                    ['item_name' => 'Kursi Roda Standar RS', 'quantity' => 5, 'uom' => 'Unit', 'estimated_unit_price' => 1800000],
                ]
            ],

            // --- DEPARTMENT 4 (HUMAN RELATIONSHIP / HR) ---
            [
                'department_id' => 4,
                'title' => 'Merchandise Welcome Kit Karyawan Baru',
                'description' => 'Tumbler dan Buku Catatan untuk batch orientasi.',
                'status' => 'draft',
                'items' => [
                    ['item_name' => 'Tumbler Stainless Custom', 'quantity' => 30, 'uom' => 'Pcs', 'estimated_unit_price' => 85000],
                    ['item_name' => 'Notebook Hardcover A5', 'quantity' => 30, 'uom' => 'Pcs', 'estimated_unit_price' => 45000],
                ]
            ],
            [
                'department_id' => 4,
                'title' => 'Kursi Ergonomis Ruang Meeting HR',
                'description' => 'Mengganti kursi lama yang sudah rusak.',
                'status' => 'approved',
                'items' => [
                    ['item_name' => 'Kursi Kantor Ergonomis', 'quantity' => 10, 'uom' => 'Unit', 'estimated_unit_price' => 1200000],
                ]
            ],

            // --- DEPARTMENT 5 (FINANCE) ---
            [
                'department_id' => 2,
                'title' => 'Brankas Tahan Api Ukuran Sedang',
                'description' => 'Untuk penyimpanan dokumen fisik penting.',
                'status' => 'submitted',
                'items' => [
                    ['item_name' => 'Fireproof Safe Box 50L', 'quantity' => 1, 'uom' => 'Unit', 'estimated_unit_price' => 7500000],
                ]
            ],
            [
                'department_id' => 3,
                'title' => 'Mesin Penghancur Kertas (Paper Shredder)',
                'description' => 'Kebutuhan pemusnahan dokumen rahasia.',
                'status' => 'approved',
                'items' => [
                    ['item_name' => 'Heavy Duty Paper Shredder', 'quantity' => 2, 'uom' => 'Unit', 'estimated_unit_price' => 3200000],
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($dummyPRs as $index => $data) {
                $totalCost = 0;
                foreach ($data['items'] as $item) {
                    $totalCost += ($item['quantity'] * $item['estimated_unit_price']);
                }

                $pr = PurchaseRequisition::create([
                    'pr_number' => 'PR-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                    'department_id' => $data['department_id'],
                    'user_id' => $userId,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'estimated_total_cost' => $totalCost,
                    'status' => $data['status'],
                    'approved_at' => $data['status'] === 'approved' ? now() : null,
                    'approved_by' => $data['status'] === 'approved' ? $userId : null,
                ]);

                foreach ($data['items'] as $item) {
                    $pr->items()->create([
                        'item_name' => $item['item_name'],
                        'quantity' => $item['quantity'],
                        'uom' => $item['uom'],
                        'estimated_unit_price' => $item['estimated_unit_price'],
                    ]);
                }

                $budget = Budget::where('department_id', $pr->department_id)
                                ->where('fiscal_year', date('Y'))
                                ->first();

                if ($budget) {
                    if ($pr->status === 'submitted') {
                        $budget->increment('reserved_amount', $totalCost);
                    } elseif ($pr->status === 'approved') {
                        $budget->increment('used_amount', $totalCost);
                    }
                }
            }
            DB::commit();
            $this->command->info('9 Dummy Purchase Requisitions berhasil dibuat beserta sinkronisasi anggarannya!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
