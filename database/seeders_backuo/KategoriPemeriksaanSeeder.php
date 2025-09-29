<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriPemeriksaanSeeder extends Seeder
{
    public function run()
    {
        DB::table('kategori_pemeriksaans')->insert([
            ['pemeriksaan' => 'Hemoglobin (Hb)', 'satuan' => 'g/dL', 'nilai_normal' => '12-16', 'biaya' => 50000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Hitung Leukosit', 'satuan' => 'x10³/μL', 'nilai_normal' => '4-10', 'biaya' => 55000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Hitung Trombosit', 'satuan' => 'x10³/μL', 'nilai_normal' => '150-400', 'biaya' => 60000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Gula Darah Puasa', 'satuan' => 'mg/dL', 'nilai_normal' => '<100', 'biaya' => 70000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Gula Darah Sewaktu', 'satuan' => 'mg/dL', 'nilai_normal' => '<200', 'biaya' => 75000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Gula Darah 2 Jam PP', 'satuan' => 'mg/dL', 'nilai_normal' => '<140', 'biaya' => 80000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Kolesterol Total', 'satuan' => 'mg/dL', 'nilai_normal' => '<200', 'biaya' => 85000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'HDL Kolesterol', 'satuan' => 'mg/dL', 'nilai_normal' => '>40', 'biaya' => 90000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'LDL Kolesterol', 'satuan' => 'mg/dL', 'nilai_normal' => '<130', 'biaya' => 95000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Trigliserida', 'satuan' => 'mg/dL', 'nilai_normal' => '<150', 'biaya' => 100000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Fungsi Hati (SGOT)', 'satuan' => 'U/L', 'nilai_normal' => '0-40', 'biaya' => 110000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Fungsi Hati (SGPT)', 'satuan' => 'U/L', 'nilai_normal' => '0-35', 'biaya' => 115000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Fungsi Ginjal (Ureum)', 'satuan' => 'mg/dL', 'nilai_normal' => '10-50', 'biaya' => 120000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Fungsi Ginjal (Kreatinin)', 'satuan' => 'mg/dL', 'nilai_normal' => '0.6-1.3', 'biaya' => 125000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Asam Urat', 'satuan' => 'mg/dL', 'nilai_normal' => '3.5-7.2', 'biaya' => 130000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Urinalisis', 'satuan' => '-', 'nilai_normal' => 'Normal', 'biaya' => 70000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Tes Kehamilan (Urine)', 'satuan' => '-', 'nilai_normal' => 'Negatif', 'biaya' => 80000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Pemeriksaan Malaria', 'satuan' => '-', 'nilai_normal' => 'Negatif', 'biaya' => 95000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Tes Widal', 'satuan' => '-', 'nilai_normal' => 'Negatif', 'biaya' => 100000, 'created_at' => now(), 'updated_at' => now()],
            ['pemeriksaan' => 'Tes HIV', 'satuan' => '-', 'nilai_normal' => 'Negatif', 'biaya' => 150000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
