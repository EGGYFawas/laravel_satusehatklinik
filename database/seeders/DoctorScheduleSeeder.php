<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil dokter pertama yang kita buat di DoctorSeeder
        $doctor1 = Doctor::find(1);
        if ($doctor1) {
            // PERBAIKAN DI SINI: Menggunakan nama relasi 'doctorSchedules()' yang benar
            $doctor1->doctorSchedules()->createMany([
                ['day_of_week' => 'Senin', 'start_time' => '08:00', 'end_time' => '12:00'],
                ['day_of_week' => 'Rabu', 'start_time' => '08:00', 'end_time' => '12:00'],
                ['day_of_week' => 'Jumat', 'start_time' => '08:00', 'end_time' => '12:00'],
            ]);
        }

        // Ambil dokter kedua
        $doctor2 = Doctor::find(2);
        if ($doctor2) {
            // PERBAIKAN DI SINI: Menggunakan nama relasi 'doctorSchedules()' yang benar
            $doctor2->doctorSchedules()->createMany([
                ['day_of_week' => 'Selasa', 'start_time' => '13:00', 'end_time' => '17:00'],
                ['day_of_week' => 'Kamis', 'start_time' => '13:00', 'end_time' => '17:00'],
            ]);
        }
    }
}

