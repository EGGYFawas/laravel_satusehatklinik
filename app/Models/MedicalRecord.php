<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinic_queue_id',
        'patient_id',
        'doctor_id',
        'checkup_date',
        'doctor_notes',
    ];

    /**
     * Mendapatkan data antrian klinik asal rekam medis ini.
     */
    public function clinicQueue()
    {
        return $this->belongsTo(ClinicQueue::class);
    }

    /**
     * Mendapatkan data pasien pemilik rekam medis.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Mendapatkan data dokter yang menangani.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Mendapatkan resep yang terkait dengan rekam medis ini.
     */
    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

    /**
     * Relasi many-to-many ke DiagnosisTag.
     * Satu rekam medis bisa memiliki banyak tag diagnosa.
     */
    public function diagnosisTags()
    {
        return $this->belongsToMany(DiagnosisTag::class, 'record_diagnoses');
    }
}

