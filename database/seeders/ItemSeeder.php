<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'item_code' => 'IT-LPT-001',
                'name' => 'Laptop Business Core i7 16GB RAM',
                'category' => 'IT Equipment',
                'uom' => 'Unit',
                'estimated_price' => 15500000,
            ],
            [
                'item_code' => 'IT-PRN-002',
                'name' => 'Printer Laserjet Monochrome',
                'category' => 'IT Equipment',
                'uom' => 'Unit',
                'estimated_price' => 3200000,
            ],
            [
                'item_code' => 'MED-STT-003',
                'name' => 'Stetoskop Littman Classic III',
                'category' => 'Medical Equipment',
                'uom' => 'Pcs',
                'estimated_price' => 1850000,
            ],
            [
                'item_code' => 'MED-MSK-004',
                'name' => 'Masker Medis 3-Ply (Box isi 50)',
                'category' => 'Medical Supplies',
                'uom' => 'Box',
                'estimated_price' => 45000,
            ],
            [
                'item_code' => 'OFC-KRT-005',
                'name' => 'Kertas A4 80gr Sinar Dunia',
                'category' => 'Office Supplies',
                'uom' => 'Rim',
                'estimated_price' => 55000,
            ],
            [
                'item_code' => 'IT-SVR-006',
                'name' => 'Server Rackmount 2U Xeon Gold',
                'category' => 'IT Equipment',
                'uom' => 'Unit',
                'estimated_price' => 85000000,
            ],
            [
                'item_code' => 'MED-BED-007',
                'name' => 'Hospital Bed Electric 3 Crank',
                'category' => 'Medical Furniture',
                'uom' => 'Unit',
                'estimated_price' => 22000000,
            ],
            [
                'item_code' => 'MED-SYR-008',
                'name' => 'Spuit / Syringe 3cc (Box isi 100)',
                'category' => 'Medical Supplies',
                'uom' => 'Box',
                'estimated_price' => 120000,
            ],
            [
                'item_code' => 'OFC-CHR-009',
                'name' => 'Kursi Kerja Ergonomis Staff',
                'category' => 'Office Furniture',
                'uom' => 'Unit',
                'estimated_price' => 1250000,
            ],
            [
                'item_code' => 'MNT-AC-010',
                'name' => 'AC Split 1.5 PK Inverter',
                'category' => 'Maintenance',
                'uom' => 'Unit',
                'estimated_price' => 5800000,
            ],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(
                ['item_code' => $item['item_code']],
                $item
            );
        }
    }
}
