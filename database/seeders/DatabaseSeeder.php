<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Urutan pemanggilan seeder ini sangat penting untuk mencegah error
        // karena adanya ketergantungan antar tabel (foreign key).
        $this->call([
            // =================================================================
            // TAHAP 1: MASTER DATA FUNDAMENTAL
            // Seeder ini harus dijalankan paling awal karena tidak memiliki
            // ketergantungan dan menjadi dasar bagi seeder lainnya.
            // =================================================================
            RoleSeeder::class,
            PoliSeeder::class,
            MedicineSeeder::class,
            DiagnosisTagSeeder::class,

            // =================================================================
            // TAHAP 2: AKUN PENGGUNA & PROFIL
            // Seeder ini membuat data di tabel 'users' dan tabel profil terkait.
            // Bergantung pada data dari TAHAP 1.
            // =================================================================
            AdminUserSeeder::class,      // Membutuhkan Role 'admin' dari RoleSeeder
            DoctorSeeder::class,       // Membutuhkan Role 'dokter' dan data dari PoliSeeder
            PatientSeeder::class,      // Membutuhkan Role 'pasien'

            // =================================================================
            // TAHAP 3: DATA TAMBAHAN & TRANSAKSIONAL
            // Seeder ini bergantung pada data pengguna yang sudah dibuat di TAHAP 2.
            // =================================================================
            DoctorScheduleSeeder::class, // Membutuhkan data Dokter yang sudah ada
            ArticleSeeder::class,        // Membutuhkan User Admin sebagai penulis
            
            // --- [PENAMBAHAN BARU] ---
            // Seeder untuk mengisi sample data antrean klinik
            // Ini diletakkan di paling akhir TAHAP 3 karena membutuhkan data
            // Pasien, Dokter, Poli, dan Admin/Petugas.
            ClinicQueueSeeder::class,
        ]);
    }
}

