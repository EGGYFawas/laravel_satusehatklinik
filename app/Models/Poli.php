<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    use HasFactory;

    protected $table = 'polis';

    /**
     * Properti $fillable disesuaikan dengan ERD ('name' dan 'code').
     */
    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Relasi one-to-many ke Doctor.
     * Satu poli bisa memiliki banyak dokter.
     */
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * Relasi one-to-many ke ClinicQueue.
     * Satu poli bisa memiliki banyak antrian.
     */
    public function clinicQueues()
    {
        return $this->hasMany(ClinicQueue::class);
    }
}
