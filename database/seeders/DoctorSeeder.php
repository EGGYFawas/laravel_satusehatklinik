<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- BAGIAN YANG HILANG ADA DI SINI ---
        // Mendefinisikan data dokter dalam sebuah array.
        $doctors = [
            [
                'user' => [
                    'full_name' => 'Dr. Budi Santoso',
                    'email' => 'dr.budi@klinik.com',
                ],
                'doctor' => [
                    'poli_id' => 1, // Pastikan ID ini sesuai dengan Poli Umum
                    'specialization' => 'Dokter Umum',
                    'license_number' => 'DOK/UMUM/001',
                ],
            ],
            [
                'user' => [
                    'full_name' => 'Dr. Siti Aminah',
                    'email' => 'dr.siti@klinik.com',
                ],
                'doctor' => [
                    'poli_id' => 3, // Pastikan ID ini sesuai dengan Poli Gigi
                    'specialization' => 'Dokter Gigi',
                    'license_number' => 'DOK/GIGI/002',
                ],
            ],
        ];
        // --- AKHIR DARI BAGIAN YANG HILANG ---

        // Loop akan berjalan normal karena $doctors sudah didefinisikan.
        foreach ($doctors as $doc) {
            $user = User::firstOrCreate(
                ['email' => $doc['user']['email']],
                [
                    'full_name' => $doc['user']['full_name'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole('dokter');

            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                $doc['doctor']
            );
        }
    }
}

