<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyQueue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinic_queue_id',
        'prescription_id',
        'pharmacy_queue_number',
        'status',
        'entry_time',
        'start_racik_time',
        'finish_racik_time',
        'taken_time',
    ];

    /**
     * Mendapatkan data antrian klinik asal antrian farmasi ini.
     */
    public function clinicQueue()
    {
        return $this->belongsTo(ClinicQueue::class);
    }

    /**
     * Mendapatkan data resep yang terkait dengan antrian farmasi ini.
     */
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}

