<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $table = 'medical_records';

    protected $fillable = [
        'clinic_queue_id',
        'patient_id',
        'doctor_id',
        'checkup_date',
        'doctor_notes',
        'blood_pressure',
        'heart_rate',
        'respiratory_rate',
        'temperature',
        'oxygen_saturation',
        'physical_examination_notes',
        
        // [PENTING UNTUK DOKTER]
        // Data ini disimpan tapi nanti TIDAK PERLU ditampilkan di view Pasien
        'primary_icd10_code',
        'primary_icd10_name',
    ];

    public function clinicQueue()
    {
        return $this->belongsTo(ClinicQueue::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

    /**
     * Relasi many-to-many ke DiagnosisTag.
     * Ini yang akan ditampilkan ke Pasien sebagai "Diagnosis".
     */
    public function diagnosisTags()
    {
        // [FIXED RELASI]
        // Menggunakan parameter lengkap agar Laravel tidak salah tebak nama tabel/kolom
        return $this->belongsToMany(
            DiagnosisTag::class, 
            'record_diagnoses',      // Nama Tabel Pivot
            'medical_record_id',     // Foreign Key model INI (MedicalRecord) di pivot
            'diagnosis_tag_id'       // Foreign Key model LAWAN (DiagnosisTag) di pivot
        ); 
    }
    
    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'prescription_details')
                    ->using(PrescriptionDetail::class)
                    ->withPivot('quantity', 'dosage');
    }
}