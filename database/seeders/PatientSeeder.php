<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- BAGIAN YANG HILANG ADA DI SINI ---
        // Mendefinisikan data pasien dalam sebuah array.
        $patients = [
            [
                'user' => [
                    'full_name' => 'Agus Setiawan',
                    'email' => 'agus.setiawan@example.com',
                ],
                'patient' => [
                    'nik' => '3578010101900001',
                    'date_of_birth' => '1990-01-01',
                    'gender' => 'Laki-laki',
                    'address' => 'Jl. Merdeka No. 10, Surabaya',
                    'phone_number' => '081234567890',
                ],
            ],
            [
                'user' => [
                    'full_name' => 'Dewi Lestari',
                    'email' => 'dewi.lestari@example.com',
                ],
                'patient' => [
                    'nik' => '3578010202920002',
                    'date_of_birth' => '1992-02-02',
                    'gender' => 'Perempuan',
                    'address' => 'Jl. Pahlawan No. 20, Surabaya',
                    'phone_number' => '081234567891',
                ],
            ],
        ];
        // --- AKHIR DARI BAGIAN YANG HILANG ---

        // Loop akan berjalan normal karena $patients sudah didefinisikan.
        foreach ($patients as $pat) {
            $user = User::firstOrCreate(
                ['email' => $pat['user']['email']],
                [
                    'full_name' => $pat['user']['full_name'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole('pasien');

            Patient::firstOrCreate(
                ['user_id' => $user->id],
                $pat['patient']
            );
        }
    }
}

