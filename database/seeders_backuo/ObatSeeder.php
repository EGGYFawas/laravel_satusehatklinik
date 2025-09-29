<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObatSeeder extends Seeder
{
    public function run()
    {
        DB::table('obats')->insert([
            ['nama_obat' => 'Paracetamol', 'harga_beli' => 5000, 'harga_jual' => 7000, 'stok' => 100, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Amoxicillin', 'harga_beli' => 8000, 'harga_jual' => 10000, 'stok' => 50, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Ibuprofen', 'harga_beli' => 6000, 'harga_jual' => 8500, 'stok' => 80, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Cetirizine', 'harga_beli' => 4000, 'harga_jual' => 6000, 'stok' => 60, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Metformin', 'harga_beli' => 3000, 'harga_jual' => 5000, 'stok' => 70, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Omeprazole', 'harga_beli' => 7000, 'harga_jual' => 9500, 'stok' => 40, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Simvastatin', 'harga_beli' => 8000, 'harga_jual' => 11000, 'stok' => 30, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Captopril', 'harga_beli' => 2000, 'harga_jual' => 4000, 'stok' => 90, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Amlodipine', 'harga_beli' => 5000, 'harga_jual' => 7500, 'stok' => 100, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Dexamethasone', 'harga_beli' => 4000, 'harga_jual' => 6000, 'stok' => 60, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Lansoprazole', 'harga_beli' => 7000, 'harga_jual' => 10000, 'stok' => 50, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Azithromycin', 'harga_beli' => 9000, 'harga_jual' => 12000, 'stok' => 30, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Clarithromycin', 'harga_beli' => 8500, 'harga_jual' => 11000, 'stok' => 25, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Hydrochlorothiazide', 'harga_beli' => 2500, 'harga_jual' => 4500, 'stok' => 80, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Losartan', 'harga_beli' => 5500, 'harga_jual' => 7500, 'stok' => 60, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Furosemide', 'harga_beli' => 3000, 'harga_jual' => 5000, 'stok' => 90, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Spironolactone', 'harga_beli' => 4500, 'harga_jual' => 7000, 'stok' => 70, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Atorvastatin', 'harga_beli' => 9000, 'harga_jual' => 12000, 'stok' => 40, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Alprazolam', 'harga_beli' => 6000, 'harga_jual' => 8500, 'stok' => 50, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Diazepam', 'harga_beli' => 7000, 'harga_jual' => 9500, 'stok' => 45, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Ciprofloxacin', 'harga_beli' => 8000, 'harga_jual' => 10500, 'stok' => 55, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Ketorolac', 'harga_beli' => 5000, 'harga_jual' => 7500, 'stok' => 70, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Meloxicam', 'harga_beli' => 4000, 'harga_jual' => 6000, 'stok' => 80, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Celecoxib', 'harga_beli' => 9000, 'harga_jual' => 12000, 'stok' => 40, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Ranitidine', 'harga_beli' => 2000, 'harga_jual' => 4000, 'stok' => 100, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Metoclopramide', 'harga_beli' => 3000, 'harga_jual' => 5000, 'stok' => 90, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Doxycycline', 'harga_beli' => 8000, 'harga_jual' => 11000, 'stok' => 30, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Levofloxacin', 'harga_beli' => 8500, 'harga_jual' => 11500, 'stok' => 40, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Salmeterol', 'harga_beli' => 7000, 'harga_jual' => 9500, 'stok' => 60, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_obat' => 'Budesonide', 'harga_beli' => 6000, 'harga_jual' => 8500, 'stok' => 70, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
