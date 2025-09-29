<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleOverride extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'doctor_id',
        'override_date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'created_by',
    ];

    /**
     * Mendapatkan data dokter yang jadwalnya di-override.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Mendapatkan data user (admin/staf) yang membuat override ini.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

