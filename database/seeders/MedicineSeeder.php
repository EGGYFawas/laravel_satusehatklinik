<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            ['name' => 'Paracetamol 500mg', 'unit' => 'Tablet', 'stock' => 1000],
            ['name' => 'Amoxicillin 500mg', 'unit' => 'Kapsul', 'stock' => 500],
            ['name' => 'OBH Combi Batuk Flu', 'unit' => 'Botol 100ml', 'stock' => 200],
        ];

        foreach ($medicines as $med) {
            Medicine::firstOrCreate(['name' => $med['name']], $med);
        }
    }
}

