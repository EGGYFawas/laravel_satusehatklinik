<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PractitionerSeeder extends Seeder
{
    public function run()
    {
        DB::table('practitioners')->insert([
            [
                'namaPractitioner' => 'ARYATI PRIMA PAWESTRI',
                'nikPractitioner' => '3174036510860006',
                'practitioner_group_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'namaPractitioner' => 'Voigt',
                'nikPractitioner' => '367400001111222',
                'practitioner_group_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
