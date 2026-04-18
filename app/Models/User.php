<?php

namespace App\Models;

// 1. Import interface MustVerifyEmail untuk fitur verifikasi email
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// === AWAL TAMBAHAN FILAMENT ===
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
// === AKHIR TAMBAHAN FILAMENT ===

// 2. Tambahkan 'MustVerifyEmail' di sini (dipisahkan koma dengan FilamentUser)
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'full_name', // INI SUDAH BENAR
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
     */
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Relasi one-to-one ke Doctor.
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }


    // === METHOD UNTUK FILAMENT ===

    /**
     * Gerbang keamanan untuk panel admin.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Pastikan hanya admin yang bisa akses panel Filament
        return $this->hasRole('admin');
    }

    /**
     * SOLUSI 1: Untuk `if ($user instanceof HasName)`
     * Memberitahu Filament nama apa yang harus ditampilkan.
     */
    public function getFilamentName(): string
    {
        // Failsafe '??' tetap dipakai untuk jaminan 100% anti-null
        return $this->full_name ?? 'Admin';
    }

    /**
     * [!!! TAMBAHAN SOLUSI LAPIS BAJA !!!]
     * SOLUSI 2: Untuk 'fallback' Baris 594
     * * Ini adalah "Accessor". Laravel akan otomatis memanggil
     * method ini jika ada kode yang mencoba mengakses '$user->name'.
     *
     * Kita "tipu" Filament dengan mengembalikan 'full_name'
     * saat dia mencari 'name'.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        // Kembalikan 'full_name' sebagai 'name'
        return $this->full_name ?? 'Admin';
    }

    // === AKHIR METHOD UNTUK FILAMENT ===
}