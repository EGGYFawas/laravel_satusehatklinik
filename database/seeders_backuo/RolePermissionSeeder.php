<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        // Ini penting untuk memastikan cache Spatie diperbarui
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat semua role yang dibutuhkan oleh aplikasi Anda
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'dokter']);
        Role::create(['name' => 'petugas loket apotek']);
        Role::create(['name' => 'pasien']);
        
        // Catatan: Kita tidak akan membuat permission atau memberikan role ke user tertentu di sini.
        // Seeder ini hanya bertugas untuk MENYIAPKAN daftar role yang ada.
        // Pemberian role 'pasien' terjadi saat registrasi.
        // Pemberian role 'admin', 'dokter', dll, sebaiknya dilakukan manual melalui panel admin.
    }
}
