<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Icd10;

class Icd10Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 1. Kosongkan tabel dulu biar gak duplikat kalau dijalankan ulang
        Icd10::truncate();

        // 2. Lokasi file CSV (Pastikan file ada di folder database/seeders/)
        $csvFile = database_path('seeders/icd10.csv'); 

        if (!file_exists($csvFile)) {
            $this->command->error("File CSV tidak ditemukan di: $csvFile");
            return;
        }

        $file = fopen($csvFile, 'r');
        
        // Lewati baris pertama (Header: CODE,DISPLAY,VERSION)
        fgetcsv($file); 

        $chunkSize = 1000; // Kita masukkan per 1000 data biar RAM aman
        $data = [];
        
        $this->command->info('Memulai import data ICD-10...');

        while (($row = fgetcsv($file)) !== false) {
            // Mapping sesuai struktur CSV kamu:
            // $row[0] = CODE (contoh: A00)
            // $row[1] = DISPLAY (contoh: Cholera)
            // $row[2] = VERSION (kita abaikan ini)

            // Pastikan row tidak kosong
            if (!empty($row[0]) && !empty($row[1])) {
                $data[] = [
                    'code' => $row[0],
                    'name' => $row[1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Jika tampungan sudah mencapai 1000, masukkan ke DB
            if (count($data) >= $chunkSize) {
                DB::table('icd10s')->insert($data);
                $data = []; // Reset tampungan
                $this->command->info('Berhasil mengimpor batch data...');
            }
        }

        // Masukkan sisa data terakhir (yang kurang dari 1000)
        if (!empty($data)) {
            DB::table('icd10s')->insert($data);
        }

        fclose($file);
        $this->command->info('SELESAI! Semua data ICD-10 berhasil masuk.');
    }
}