<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosisTag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tag_name',
        'description',
    ];

    /**
     * Relasi many-to-many ke MedicalRecord.
     * Satu tag diagnosa bisa dimiliki oleh banyak rekam medis.
     */
    public function medicalRecords()
    {
        return $this->belongsToMany(MedicalRecord::class, 'record_diagnoses');
    }
}

