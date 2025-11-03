<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClinicQueue;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Poli;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClinicQueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil data master yang valid
        // Kita butuh data ini agar foreign key-nya tidak error.
        $patients = Patient::pluck('id');
        $doctors = Doctor::pluck('id');
        $polis = Poli::pluck('id');
        
        // Kita ambil 1 admin/petugas untuk field 'registered_by_user_id'
        $registrar = User::role(['admin', 'petugas loket'])->first();

        // 2. Validasi data master
        // Jika salah satu data master kosong, seeder tidak bisa jalan.
        if ($patients->isEmpty() || $doctors->isEmpty() || $polis->isEmpty() || !$registrar) {
            $this->command->error('Data master (Pasien/Dokter/Poli/Admin/Petugas) tidak ditemukan.');
            $this->command->warn('Pastikan Anda sudah menjalankan seeder untuk Pasien, Dokter, Poli, dan User (dengan role).');
            $this->command->info('Seeder ClinicQueue dibatalkan.');
            return;
        }

        // --- Logika Inti Seeder ---

        // 3. Tentukan Rentang Tanggal (Sesuai Permintaan)
        
        // [MODIFIKASI PENTING]
        // Kita gunakan tahun saat ini (Carbon::now()->year) agar seeder ini
        // selalu relevan dengan chart-mu (yang mengambil data 7 hari terakhir).
        $currentYear = Carbon::now()->year; 
        
        $startDate = Carbon::create($currentYear, 11, 4); // 4 November (Tahun Ini)
        $endDate = Carbon::create($currentYear, 11, 8);   // 8 November (Tahun Ini)

        $this->command->info("Membuat data ClinicQueue dari {$startDate->toDateString()} s/d {$endDate->toDateString()}...");

        $complaints = ['Demam tinggi dan menggigil', 'Sakit tenggorokan dan batuk', 'Nyeri dada sebelah kiri', 'Pusing berputar (Vertigo)', 'Mata merah dan berair', 'Kontrol rutin diabetes', 'Cek tekanan darah tinggi', 'Sakit maag akut'];
        $relationships = ['Diri Sendiri', 'Anak', 'Orang Tua', 'Pasangan'];

        // 4. Loop untuk setiap hari
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) 
        {
            // 5. Tentukan Jumlah Kunjungan (Random 10-50 per hari)
            $visitCount = rand(10, 50);
            $this->command->info("--> Membuat {$visitCount} kunjungan untuk tanggal: " . $date->toDateString());

            // 6. Loop untuk setiap kunjungan
            for ($i = 1; $i <= $visitCount; $i++) {
                
                // 7. Buat Timestamp Realistis
                // Asumsi jam pendaftaran antara 08:00 - 16:00
                $registrationTime = $date->copy()->setTime(rand(8, 15), rand(0, 59), rand(0, 59));
                
                // Waktu tunggu (10 - 45 menit)
                $callTime = $registrationTime->copy()->addMinutes(rand(10, 45));
                
                // Lama periksa (10 - 20 menit)
                $finishTime = $callTime->copy()->addMinutes(rand(10, 20));

                $status = 'SELESAI'; // Kita asumsikan semua data seeder selesai

                ClinicQueue::create([
                    'patient_id' => $patients->random(),
                    'poli_id' => $polis->random(),
                    'doctor_id' => $doctors->random(),
                    'registered_by_user_id' => $registrar->id,
                    'queue_number' => 'A-' . str_pad($i, 3, '0', STR_PAD_LEFT), // Contoh: A-001, A-002
                    'chief_complaint' => $complaints[array_rand($complaints)],
                    'patient_relationship' => $relationships[array_rand($relationships)],
                    'status' => $status,
                    'is_follow_up' => false,
                    
                    // 8. Input Timestamp (Sangat Penting)
                    'registration_time' => $registrationTime,
                    'call_time' => $callTime,
                    'finish_time' => $finishTime,
                    'cancellation_time' => null, // Karena status 'SELESAI'
                    
                    // 9. Atur created_at agar SAMA DENGAN registration_time
                    // Ini PENTING agar chart-mu (yang pakai created_at) bisa membacanya
                    'created_at' => $registrationTime, 
                    'updated_at' => $finishTime,
                ]);
            }
        }

        $this->command->info('Seeder ClinicQueue (sample data) berhasil dijalankan.');
    }
}

