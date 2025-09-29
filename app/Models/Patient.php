<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    /**
     * Properti $fillable disesuaikan total dengan ERD terbaru.
     */
    protected $fillable = [
        'user_id',
        'nik',
        // 'full_name' tidak perlu di sini karena data nama diambil dari tabel User
        'date_of_birth',
        'gender',
        'address',
        'phone_number',
        'blood_type',
        'known_allergies',
        'chronic_diseases',
    ];

    /**
     * Relasi one-to-one (inverse) ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi one-to-many ke ClinicQueue.
     * Satu pasien bisa memiliki banyak antrian klinik.
     */
    public function clinicQueues()
    {
        return $this->hasMany(ClinicQueue::class);
    }
}
