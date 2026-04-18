<?php

namespace Database\Seeders;

use App\Models\DiagnosisTag;
use Illuminate\Database\Seeder;

class DiagnosisTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['tag_name' => 'Hipertensi Esensial (Primer)', 'description' => 'I10'],
            ['tag_name' => 'Diabetes Melitus Tipe 2', 'description' => 'E11'],
            ['tag_name' => 'Common Cold', 'description' => 'J00'],
        ];

        foreach ($tags as $tag) {
            DiagnosisTag::firstOrCreate(['tag_name' => $tag['tag_name']], $tag);
        }
    }
}

