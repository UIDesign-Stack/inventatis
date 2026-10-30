<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // master data
            'category.manage',
            'supplier.manage',
            'item.manage',
            'teacher.manage',

            // barang masuk
            'stock-in.create',
            'stock-in.report',

            // peminjaman
            'loan.view',
            'loan.create',
            'loan.approve',
            'loan.handover',

            // barang keluar
            'stock-out.report',

            // manajemen user
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Operator: stok masuk dari suplier, master data, dan manajemen user
        $operator = Role::firstOrCreate(['name' => 'operator']);
        $operator->syncPermissions([
            'category.manage',
            'supplier.manage',
            'item.manage',
            'teacher.manage',
            'stock-in.create',
            'stock-in.report',
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
        ]);

        // Sarpras: mencatat, memutuskan (ACC/tolak/pending), dan serah terima
        $sarpras = Role::firstOrCreate(['name' => 'sarpras']);
        $sarpras->syncPermissions([
            'loan.view',
            'loan.create',
            'loan.approve',
            'loan.handover',
            'stock-out.report',
        ]);

        // akun contoh (hapus/ganti sesuai kebutuhan)
        User::firstOrCreate(
            ['email' => 'operator@example.com'],
            ['name' => 'Operator', 'password' => bcrypt('password')]
        )->assignRole($operator);

        User::firstOrCreate(
            ['email' => 'sarpras@example.com'],
            ['name' => 'Sarpras', 'password' => bcrypt('password')]
        )->assignRole($sarpras);
    }
}
