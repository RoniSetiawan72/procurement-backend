<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-users', 'manage-roles',
            'create-pr', 'edit-pr', 'view-pr',
            'approve-pr', 'reject-pr',
            'create-po', 'edit-po', 'view-po',
            'approve-po', 'reject-po',
            'manage-tenders', 'select-winner',
            'submit-bid'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $admin = Role::create(['name' => 'Administrator']);
        $admin->givePermissionTo(Permission::all());

        $staff = Role::create(['name' => 'Staff Department']);
        $staff->givePermissionTo(['create-pr', 'edit-pr', 'view-pr', 'view-po']);

        $manager = Role::create(['name' => 'Manager']);
        $manager->givePermissionTo([
            'view-pr', 'approve-pr', 'reject-pr',
            'view-po', 'approve-po', 'reject-po'
        ]);

        $procurement = Role::create(['name' => 'Procurement Officer']);
        $procurement->givePermissionTo([
            'view-pr',
            'create-po', 'edit-po', 'view-po',
            'manage-tenders', 'select-winner'
        ]);

        $vendor = Role::create(['name' => 'Vendor']);
        $vendor->givePermissionTo(['submit-bid']);
    }
}
