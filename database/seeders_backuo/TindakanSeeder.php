<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TindakanSeeder extends Seeder
{
    public function run()
    {
        DB::table('tindakans')->insert([
            ['tindakan' => 'Konsultasi Dokter Umum', 'biaya' => 50000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Konsultasi Dokter Spesialis', 'biaya' => 100000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Tekanan Darah', 'biaya' => 25000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Gula Darah', 'biaya' => 30000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Kolesterol', 'biaya' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan EKG', 'biaya' => 150000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'USG Kandungan', 'biaya' => 200000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan THT', 'biaya' => 75000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Mata', 'biaya' => 60000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Gigi', 'biaya' => 80000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pembersihan Karang Gigi', 'biaya' => 120000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pencabutan Gigi', 'biaya' => 100000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Tambal Gigi', 'biaya' => 150000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Kehamilan', 'biaya' => 175000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Kulit', 'biaya' => 90000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Jantung', 'biaya' => 200000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Saraf', 'biaya' => 180000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Bedah', 'biaya' => 220000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Pemeriksaan Anak', 'biaya' => 60000, 'created_at' => now(), 'updated_at' => now()],
            ['tindakan' => 'Fisioterapi', 'biaya' => 250000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
