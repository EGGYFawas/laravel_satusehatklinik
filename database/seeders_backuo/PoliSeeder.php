<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Poli; // Menggunakan model lebih baik daripada DB facade

class PoliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Data poli menggunakan kode unik dari standar SatuSehat FHIR
        // Ini adalah praktik terbaik untuk interoperabilitas data kesehatan.
        $polis = [
            ['kode_poli' => '001', 'nama_poli' => 'Poli Anak'],
            ['kode_poli' => '003', 'nama_poli' => 'Poli Telinga Hidung Tenggorokan'],
            ['kode_poli' => '005', 'nama_poli' => 'Poli Bedah'],
            ['kode_poli' => '012', 'nama_poli' => 'Poli Penyakit Dalam'],
            ['kode_poli' => '013', 'nama_poli' => 'Poli Mata'],
            ['kode_poli' => '018', 'nama_poli' => 'Poli Saraf'],
            ['kode_poli' => '020', 'nama_poli' => 'Poli Gigi dan Mulut'],
            ['kode_poli' => '021', 'nama_poli' => 'Poli Kulit dan Kelamin'],
            ['kode_poli' => '022', 'nama_poli' => 'Poli Jantung dan Pembuluh Darah'],
            ['kode_poli' => '026', 'nama_poli' => 'Poli Obstetri dan Ginekologi'],
            // Tambahkan poli lain jika diperlukan
        ];

        // Menggunakan firstOrCreate untuk mencegah duplikasi jika seeder dijalankan berkali-kali
        foreach ($polis as $poli) {
            Poli::firstOrCreate(['kode_poli' => $poli['kode_poli']], [
                'nama_poli' => $poli['nama_poli'],
            ]);
        }
    }
}

