<?php

namespace App\Imports;

use App\Models\Tindakan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TindakanImport implements ToModel, WithHeadingRow

{
    public function model(array $row)
    {
        return new Tindakan([
            'tindakan' => $row['tindakan'], // Nama header kolom pertama
            'biaya' => $row['biaya'],       // Nama header kolom kedua
        ]);
    }
}

