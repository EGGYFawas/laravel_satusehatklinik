<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicQueue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'poli_id',
        'doctor_id',
        'registered_by_user_id',
        'queue_number',
        'chief_complaint',
        'patient_relationship',
        'patient_relationship_custom',
        'status',
        'is_follow_up',
        'follow_up_notes',
        'registration_time',
        'call_time',
        'finish_time',
        'cancellation_time',
        'cancellation_actor',
        'cancellation_reason',
    ];

    /**
     * Mendapatkan data pasien yang mendaftar antrian.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Mendapatkan data poli tujuan.
     */
    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    /**
     * Mendapatkan data dokter tujuan.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Mendapatkan data user yang mendaftarkan antrian.
     */
    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    /**
     * Mendapatkan rekam medis yang terkait dengan antrian ini.
     */
    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }
    public function pharmacyQueue()
    {
        // Asumsi foreign key di tabel pharmacy_queues adalah 'clinic_queue_id'
        return $this->hasOne(PharmacyQueue::class);
    }
}

