<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPageContent;
use Illuminate\Support\Facades\DB;

class LandingPageContentSeeder extends Seeder
{
    public function run()
    {
        // 1. Matikan pengecekan Foreign Key sementara (Supaya bisa truncate tanpa error)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // 2. KOSONGKAN TABEL (Hapus data lama yang bikin error loading/crash)
        LandingPageContent::truncate();

        // 3. Nyalakan lagi pengecekan Foreign Key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 4. Masukkan Data Baru (Format Bersih: Gambar punya kolom sendiri)
        $contents = [
            // HERO SECTION
            [
                'key' => 'hero_title',
                'label' => 'Judul Hero Section',
                'value' => 'Kesehatan Anda Adalah Prioritas Kami', // Teks masuk Value
                'image' => null,
                'type' => 'text'
            ],
            [
                'key' => 'hero_image',
                'label' => 'Gambar Hero Utama',
                'value' => null, 
                'image' => null, // Gambar masuk kolom Image (Nanti diupload admin)
                'type' => 'image'
            ],
            
            // ABOUT SECTION
            [
                'key' => 'about_us_title',
                'label' => 'Judul Tentang Kami',
                'value' => 'Mitra Kesehatan Terpercaya untuk Keluarga Anda',
                'image' => null,
                'type' => 'text'
            ],
            [
                'key' => 'about_us_image',
                'label' => 'Gambar Tentang Kami',
                'value' => null,
                'image' => null,
                'type' => 'image'
            ],
            
            // CONTACT INFO
            [
                'key' => 'contact_phone',
                'label' => 'Nomor Telepon',
                'value' => '(021) 1234-5678',
                'image' => null,
                'type' => 'text'
            ],
        ];

        foreach ($contents as $content) {
            LandingPageContent::create($content);
        }
    }
}