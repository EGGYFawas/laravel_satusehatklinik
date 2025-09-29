<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     * Kolom 'name' diubah menjadi 'full_name' agar sesuai dengan database.
     */
    protected $fillable = [
        'full_name', // DIGANTI DARI 'name'
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi one-to-one ke Patient.
     * Satu User bisa memiliki satu profil Patient.
     */
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Relasi one-to-one ke Doctor.
     * Satu User bisa memiliki satu profil Doctor.
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }
}

