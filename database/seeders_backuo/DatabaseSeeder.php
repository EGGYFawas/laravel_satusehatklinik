<?php

namespace Database\Seeders;

use App\Models\PractitionerGroup;
use App\Models\Tkp;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
         * Data dummy untuk user admin telah dihapus dari sini.
         * Pembuatan user admin kini ditangani oleh AdminUserSeeder agar lebih terstruktur.
         */

        // Data master ini bisa tetap di sini jika hanya ada satu atau dua.
        // Ini adalah data awal yang dibutuhkan sistem, bukan data dummy.
        PractitionerGroup::firstOrCreate(['namaGroup' => 'Dokter']);
        Tkp::firstOrCreate(['kdTkp' => '1'], ['nmTkp' => "Rawat Jalan"]);

        // Panggil semua seeder lain dalam urutan yang logis.
        // RolePermissionSeeder harus dijalankan pertama agar role 'admin' tersedia
        // untuk AdminUserSeeder.
        $this->call([
            RolePermissionSeeder::class, // WAJIB PERTAMA
            AdminUserSeeder::class,      // Seeder baru untuk membuat user admin
            
            // Seeder untuk data master wilayah
            ProvinsiSeeder::class,
            KabupatenSeeder::class,
            KecamatanSeeder::class,
            KelurahanSeeder::class,

            // Seeder untuk data master aplikasi
            PoliSeeder::class,
            TindakanSeeder::class,
            KategoriPemeriksaanSeeder::class,
            ObatSeeder::class,
            DiagnosaSeeder::class,
            PractitionerSeeder::class,
            PatientSeeder::class, // Mungkin maksud Anda PasienSeeder?
        ]);
    }
}
