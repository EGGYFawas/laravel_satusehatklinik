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
            // =================================================================
            RoleSeeder::class,
            PoliSeeder::class,
            MedicineSeeder::class,
            DiagnosisTagSeeder::class,

            // =================================================================
            // TAHAP 2: AKUN PENGGUNA & PROFIL
            // =================================================================
            AdminUserSeeder::class,      
            DoctorSeeder::class,       
            PatientSeeder::class,      

            // =================================================================
            // TAHAP 3: DATA TAMBAHAN & TRANSAKSIONAL
            // =================================================================
            DoctorScheduleSeeder::class, 
            ArticleSeeder::class,        
            
            // --- [MODIFIKASI] ---
            // Kita gunakan dua seeder terpisah agar lebih rapi dan terkontrol
            // Pastikan ClinicQueueSeeder jalan DULUAN sebelum PharmacyQueueSeeder
            // ClinicQueueSeeder::class,
            // PharmacyQueueSeeder::class,
        ]);
    }
}