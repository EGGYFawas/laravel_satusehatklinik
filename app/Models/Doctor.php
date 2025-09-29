<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'poli_id',
        'specialization',
        'license_number',
    ];

    /**
     * Mendapatkan data user yang terkait dengan dokter ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan data poli tempat dokter ini bertugas.
     */
    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    /**
     * Mendapatkan semua jadwal reguler milik dokter ini.
     */
    public function doctorSchedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }
}

