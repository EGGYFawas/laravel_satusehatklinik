<?php

namespace Database\Seeders;

use App\Models\Poli;
use Illuminate\Database\Seeder;

class PoliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $polis = [
            ['code' => 'UMU', 'name' => 'Poli Umum'],
            ['code' => 'GIG', 'name' => 'Poli Gigi'],
            ['code' => 'KIA', 'name' => 'Poli KIA (Kesehatan Ibu dan Anak)'],
            ['code' => 'THT', 'name' => 'Poli THT'],
            ['code' => 'MTA', 'name' => 'Poli Mata'],
        ];

        foreach ($polis as $poli) {
            Poli::firstOrCreate(['code' => $poli['code']], ['name' => $poli['name']]);
        }
    }
}

