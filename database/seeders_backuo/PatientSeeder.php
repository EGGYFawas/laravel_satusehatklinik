<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Seeder ini akan membuat 5 data pasien dummy.
        // Setiap pasien akan memiliki akun user-nya sendiri dengan role 'pasien'.

        $patientsData = [
            [
                'name' => 'Ardianto Putra',
                'email' => 'ardianto.putra@example.com',
                'nik' => '3301010303860001',
                'gender' => 'Pria',
                'birth_date' => '1986-03-03'
            ],
            [
                'name' => 'Claudia Sintia',
                'email' => 'claudia.sintia@example.com',
                'nik' => '3301014804900002',
                'gender' => 'Wanita',
                'birth_date' => '1990-04-17'
            ],
            [
                'name' => 'Elizabeth Dior',
                'email' => 'elizabeth.dior@example.com',
                'nik' => '3301014509930003',
                'gender' => 'Wanita',
                'birth_date' => '1993-01-10'
            ],
            [
                'name' => 'Ghina Assyifa',
                'email' => 'ghina.assyifa@example.com',
                'nik' => '3301014606170005',
                'gender' => 'Wanita',
                'birth_date' => '2017-09-14'
            ],
            [
                'name' => 'Salsabilla Anjani',
                'email' => 'salsabilla.anjani@example.com',
                'nik' => '3301025209100006',
                'gender' => 'Wanita',
                'birth_date' => '2010-02-27'
            ],
        ];

        foreach ($patientsData as $data) {
            // 1. Buat entri di tabel 'users' terlebih dahulu
            $user = User::create([
                'name' => strtoupper($data['name']),
                'email' => $data['email'],
                'password' => Hash::make('password123'), // Password default untuk semua pasien dummy
            ]);

            // 2. Berikan role 'pasien' ke user yang baru dibuat
            $user->assignRole('pasien');

            // 3. Buat Nomor Rekam Medis yang unik
            $noRekamMedis = date('Ymd') . '-' . str_pad($user->id, 6, '0', STR_PAD_LEFT);

            // 4. Buat entri di tabel 'patients' yang terhubung dengan user_id
            Patient::create([
                'user_id' => $user->id,
                'no_rekam_medis' => $noRekamMedis,
                'nik' => $data['nik'],
                'gender' => $data['gender'],
                'birth_date' => $data['birth_date'],
            ]);
        }
    }
}
