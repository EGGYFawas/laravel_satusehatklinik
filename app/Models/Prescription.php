<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'medical_record_id',
        'prescription_date',
    ];

    /**
     * Mendapatkan data rekam medis asal resep ini.
     */
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    /**
     * Mendapatkan semua detail item obat dalam resep ini.
     */
    public function prescriptionDetails()
    {
        return $this->hasMany(PrescriptionDetail::class);
    }
}

