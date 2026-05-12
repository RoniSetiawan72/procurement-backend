<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->call(RolePermissionSeeder::class);
        $this->call(UserSeeder::class);

        $this->call([
            VendorSeeder::class,
            ItemSeeder::class,
            BudgetSeeder::class,
            PurchaseRequisitionSeeder::class,
        ]);
    }
}
