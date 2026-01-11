<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecordAction extends Model
{
    use HasFactory;

    // Tabel Transaksi: Menyimpan tindakan spesifik untuk pasien tertentu
    protected $table = 'medical_record_actions';

    // Penting: Guarded kosong agar bisa create() massal dari controller
    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relasi balik ke MedicalRecord
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    // Relasi ke Master MedicalAction (Opsional, untuk referensi nama asli)
    public function medicalAction()
    {
        return $this->belongsTo(MedicalAction::class);
    }
}