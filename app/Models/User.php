<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasName, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'full_name',
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

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    // =====================================================================
    // KONTROL AKSES PANEL FILAMENT
    // =====================================================================

    public function canAccessPanel(Panel $panel): bool
    {
        // 1. Jaminan Mutu: Admin Utama pasti bisa masuk
        if ($this->email === 'admin@simklinik.com') {
            return true;
        }

        // 2. Mengizinkan Super Admin, Dokter, dan Petugas Loket masuk ke Filament.
        // Penulisan 'petugas loket' (pakai spasi) disesuaikan dengan Seeder terakhir.
        // Pasien tetap tidak diizinkan masuk ke sini.
        return $this->hasAnyRole(['super_admin', 'dokter', 'petugas loket']);
    }

    // =====================================================================
    // OVERRIDE SPATIE (SUPER ADMIN FAILSAFE)
    // =====================================================================

    public function hasRole($roles, string $guard = null): bool
    {
        if ($this->email === 'admin@simklinik.com') {
            return true;
        }
        return parent::hasRole($roles, $guard);
    }

    public function hasAnyRole(...$roles): bool
    {
         if ($this->email === 'admin@simklinik.com') {
            return true;
        }
        return parent::hasAnyRole(...$roles);
    }

    // =====================================================================
    // RESOLUSI NAMA FILAMENT
    // =====================================================================

    public function getFilamentName(): string
    {
        return $this->full_name ?? 'Admin';
    }

    public function getNameAttribute(): string
    {
        return $this->full_name ?? 'Admin';
    }
}