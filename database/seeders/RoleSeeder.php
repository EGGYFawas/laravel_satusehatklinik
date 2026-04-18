<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // <-- PASTIKAN ANDA IMPORT INI

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat role Admin
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        
        // 2. Buat role Dokter
        Role::firstOrCreate(['name' => 'dokter', 'guard_name' => 'web']);

        // 3. Buat role Pasien
        Role::firstOrCreate(['name' => 'pasien', 'guard_name' => 'web']);

        // 4. Buat role Petugas Loket (INI YANG HILANG!)
        Role::firstOrCreate(['name' => 'petugas loket', 'guard_name' => 'web']);
    }
}