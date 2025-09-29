<?php

namespace App\Exports;

use App\Models\Tindakan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TindakanExport implements FromCollection, WithHeadings
{
    /**
     * Ambil data yang akan diexport.
     */
    public function collection()
    {
        return Tindakan::all(['tindakan', 'biaya']);
    }

    /**
     * Tambahkan header pada file Excel.
     */
    public function headings(): array
    {
        return ['Tindakan', 'Biaya'];
    }
}
