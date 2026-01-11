<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalAction extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai dengan migration (biasanya 'medical_actions')
    protected $table = 'medical_actions';

    protected $guarded = ['id'];

    // Cast price ke integer/float agar aman saat hitung-hitungan
    protected $casts = [
        'price' => 'decimal:2',
    ];
}