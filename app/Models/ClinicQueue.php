<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import model terkait agar relasi berfungsi
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Doctor;
use App\Models\User;
use App\Models\PharmacyQueue;
use App\Models\MedicalRecord;

class ClinicQueue extends Model
{
    use HasFactory;

    // Tabel 'clinic_queues' diasumsikan sebagai nama tabel (default convention Laravel)

    /**
     * The attributes that are mass assignable.
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
        'registration_time', // Diisi manual dengan now() saat create
        'call_time',         // Diisi saat status berubah jadi DIPANGGIL
        'finish_time',       // Diisi saat status berubah jadi SELESAI
        'cancellation_time',
        'cancellation_actor',
        'cancellation_reason',
        'check_in_time',     // Diisi saat pasien check-in/scan QR
    ];

    /**
     * The attributes that should be cast.
     * PENTING: Ini mengubah string database menjadi objek Carbon.
     */
    protected $casts = [
        'registration_time' => 'datetime',
        'check_in_time'     => 'datetime',
        'call_time'         => 'datetime',
        'finish_time'       => 'datetime',
        'cancellation_time' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'is_follow_up'      => 'boolean', // Best practice untuk kolom boolean
    ];

    // --- Relasi-relasi ---

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function registeredBy()
    {
        // Relasi ke User yang mendaftarkan (Admin/Pasien itu sendiri)
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function pharmacyQueue()
    {
        return $this->hasOne(PharmacyQueue::class);
    }
}