<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    /**
     * Properti $fillable disesuaikan untuk mendukung pendaftaran anggota keluarga.
     */
    protected $fillable = [
        'user_id',
        'nik',
        'full_name', // <-- PERBAIKAN: Menambahkan 'full_name' ke dalam fillable
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
     * Pasien yang memiliki akun.
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
