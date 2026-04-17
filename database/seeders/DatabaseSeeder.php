<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClinicSetting;
use App\Models\Poli;
use App\Models\Doctor;
use App\Models\DoctorSchedule; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =================================================================
        // TAHAP 1: BERSIHKAN CACHE SPATIE (WAJIB)
        // =================================================================
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =================================================================
        // TAHAP 2: BUAT ROLES DASAR (SESUAI REQUEST: PAKAI SPASI)
        // =================================================================
        $roleAdmin   = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $roleDokter  = Role::firstOrCreate(['name' => 'dokter', 'guard_name' => 'web']);
        
        // MENGGUNAKAN SPASI SESUAI KODEMU YANG SUDAH ADA
        $rolePetugas = Role::firstOrCreate(['name' => 'petugas loket', 'guard_name' => 'web']);
        
        // Role pasien tetap tanpa underscore
        Role::firstOrCreate(['name' => 'pasien', 'guard_name' => 'web']);

        // =================================================================
        // TAHAP 3: DATA MASTER FUNDAMENTAL (POLI & SETTING)
        // =================================================================
        ClinicSetting::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Klinik Utama Sehat Selalu',
                'primary_color' => '#3b82f6',
            ]
        );

        // Data Poli Sesuai Format Antrean (PA, PU, PG)
        $poliAnak = Poli::firstOrCreate(['code' => 'PA'], ['name' => 'Poli Anak']);
        $poliUmum = Poli::firstOrCreate(['code' => 'PU'], ['name' => 'Poli Umum']);
        $poliGigi = Poli::firstOrCreate(['code' => 'PG'], ['name' => 'Poli Gigi']);

        // =================================================================
        // TAHAP 4: PEMBUATAN AKUN DEMO
        // =================================================================

        // 1. Akun Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@simklinik.com'],
            [
                'full_name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([$roleAdmin]);

        // 2. Akun Dokter
        $userDokter = User::firstOrCreate(
            ['email' => 'dokter@simklinik.com'],
            [
                'full_name' => 'Dr. Budi Santoso',
                'password' => Hash::make('dokter123'),
                'email_verified_at' => now(),
            ]
        );
        $userDokter->syncRoles([$roleDokter]);
        
        $dokter = Doctor::firstOrCreate(
            ['user_id' => $userDokter->id],
            [
                'poli_id' => $poliUmum->id,
                'specialization' => 'Dokter Umum', 
                'license_number' => 'SIP.123.456.2026', 
            ]
        );

        // Jadwal Dokter di Hari Jumat
        if (class_exists(DoctorSchedule::class)) {
            DoctorSchedule::firstOrCreate(
                [
                    'doctor_id' => $dokter->id,
                    'day_of_week' => 'Jumat', 
                ],
                [
                    'start_time' => '08:00:00',
                    'end_time' => '16:00:00',
                    'is_active' => true,
                ]
            );
        }

        // 3. Akun Petugas Loket (Role 'petugas loket' dengan spasi)
        $userPetugas = User::firstOrCreate(
            ['email' => 'petugas@simklinik.com'],
            [
                'full_name' => 'Siti Aminah',
                'password' => Hash::make('petugas123'),
                'email_verified_at' => now(),
            ]
        );
        $userPetugas->syncRoles([$rolePetugas]);
    }
}