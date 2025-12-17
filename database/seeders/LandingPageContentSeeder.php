<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPageContent;

class LandingPageContentSeeder extends Seeder
{
    public function run()
    {
        $contents = [
            // HERO SECTION
            [
                'key' => 'hero_title',
                'label' => 'Judul Hero Section',
                'value' => 'Kesehatan Anda Adalah Prioritas Kami',
                'type' => 'text'
            ],
            [
                'key' => 'hero_image',
                'label' => 'Gambar Hero Utama',
                'value' => null, // Nanti diupload via admin
                'type' => 'image'
            ],
            
            // ABOUT SECTION
            [
                'key' => 'about_us_title',
                'label' => 'Judul Tentang Kami',
                'value' => 'Tentang Klinik Sehat',
                'type' => 'text'
            ],
            [
                'key' => 'about_us_image',
                'label' => 'Gambar Tentang Kami',
                'value' => null,
                'type' => 'image'
            ],
            
            // CONTACT INFO
            [
                'key' => 'contact_phone',
                'label' => 'Nomor Telepon',
                'value' => '(021) 1234-5678',
                'type' => 'text'
            ],
        ];

        foreach ($contents as $content) {
            LandingPageContent::updateOrCreate(['key' => $content['key']], $content);
        }
    }
}