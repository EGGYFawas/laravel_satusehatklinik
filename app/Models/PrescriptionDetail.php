<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'quantity',
        'dosage',
    ];

    /**
     * Mendapatkan data resep induk dari detail ini.
     */
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    /**
     * Mendapatkan data obat untuk item ini.
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}

