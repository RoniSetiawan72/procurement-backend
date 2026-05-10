<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'CV. Farmasi Nusantara',
                'email' => 'sales@farmasinusantara.com',
                'address' => 'Jl. Magelang Km 7, Yogyakarta',
                'tax_id' => '02.345.678.9-054.000',
                'is_active' => true,
            ],
            [
                'name' => 'PT. Alkesindo Prima Utama',
                'email' => 'info@alkesindoprima.co.id',
                'address' => 'Kawasan Industri Rungkut, Surabaya',
                'tax_id' => '03.456.789.0-604.000',
                'is_active' => true,
            ],
            [
                'name' => 'Karya Teknika Network',
                'email' => 'proyek@karyateknika.net',
                'address' => 'Jl. Ringroad Timur, Banguntapan, Bantul',
                'tax_id' => '04.567.890.1-054.000',
                'is_active' => true,
            ],
            [
                'name' => 'PT. Data Cipta Informatika',
                'email' => 'bidding@datacipta.co.id',
                'address' => 'Gedung Cyber, Jl. Kuningan Barat, Jakarta Selatan',
                'tax_id' => '05.678.901.2-014.000',
                'is_active' => true,
            ],
            [
                'name' => 'CV. Sarana Pemeliharaan RS',
                'email' => 'maintenance@saranars.com',
                'address' => 'Jl. Solo Km 8, Depok, Sleman',
                'tax_id' => '06.789.012.3-054.000',
                'is_active' => true,
            ],
            [
                'name' => 'Global Health Solutions',
                'email' => 'contact@globalhealth.id',
                'address' => 'Jl. Gatot Subroto No. 45, Bandung',
                'tax_id' => '07.890.123.4-424.000',
                'is_active' => true,
            ],
            [
                'name' => 'PT. Berkah Logistik Medis',
                'email' => 'support@berkahlogistik.com',
                'address' => 'Jl. Wates Km 3, Kasihan, Bantul',
                'tax_id' => '08.901.234.5-054.000',
                'is_active' => false,
            ],
            [
                'name' => 'Sentosa Lab Instrument',
                'email' => 'marketing@sentosalab.co.id',
                'address' => 'Kawasan Industri Candi, Semarang',
                'tax_id' => '09.012.345.6-503.000',
                'is_active' => true,
            ],
            [
                'name' => 'PT. Inovasi Server Mandiri',
                'email' => 'enterprise@inovasiserver.com',
                'address' => 'Jl. Sudirman No. 10, Jakarta Pusat',
                'tax_id' => '10.123.456.7-071.000',
                'is_active' => true,
            ]
        ];

        foreach ($vendors as $vendor) {
            Vendor::firstOrCreate(
                ['email' => $vendor['email']], // Mencegah duplikasi data berdasarkan email
                $vendor
            );
        }
    }
}
