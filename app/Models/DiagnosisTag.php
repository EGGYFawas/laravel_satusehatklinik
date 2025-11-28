<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosisTag extends Model
{
    use HasFactory;

    protected $table = 'diagnosis_tags';

    /**
     * The attributes that are mass assignable.
     * tag_name WAJIB ada di sini biar dokter bisa input tag baru.
     */
    protected $fillable = [
        'tag_name',
        'description',
    ];

    /**
     * Relasi many-to-many ke MedicalRecord.
     */
    public function medicalRecords()
    {
        // Parameter: (Model Lawan, Nama Tabel Pivot, FK Model Ini, FK Model Lawan)
        return $this->belongsToMany(
            MedicalRecord::class, 
            'record_diagnoses', 
            'diagnosis_tag_id', 
            'medical_record_id'
        );
    }
}