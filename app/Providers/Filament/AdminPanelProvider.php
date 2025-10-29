<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// === INI ADALAH IMPORT DARI BATTLE PLAN KITA ===
// KITA HANYA MENG-IMPORT WIDGET STATISTIK DASAR
use App\Filament\Widgets\DashboardStats;
// === KITA HAPUS 'KepuasanPasienChart' DARI 'use' ===
// (karena kita sepakat skip fitur kunjungan)

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin') // Path sudah kembali ke /admin
            
            // === MODIFIKASI KUNCI ===
            ->login(false) // Nonaktifkan login page Filament, kita pakai AuthController
            
            // === THEMING LOGO, WARNA, FONT (DARI REFERENSI) ===
            ->brandLogo(asset('assets/img/logo_login.png'))
            ->brandLogoHeight('3.5rem') 
            ->font('Poppins')
            ->colors([
                'primary' => '#4F46E5', // Warna dari layout lama
                'gray' => Color::Slate, // Ganti "slate-100" jadi Slate
            ])
            
            // === IKON SUDAH KEMBALI KE DEFAULT (HEROICONS) ===
            // Tidak ada 'plugin' atau 'defaultIconPack'
            
            // === PENDAFTARAN HALAMAN & RESOURCE ===
            // Ini akan otomatis menemukan semua Resource-mu
            // (Patient, Doctor, Petugas, Obat, Article, DoctorSchedule)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            
            // === PENDAFTARAN WIDGET KUSTOM (FOKUS) ===
            // Kita hanya mendaftarkan widget statistik dasar
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                DashboardStats::class,
                // === 'KepuasanPasienChart' DIHAPUS DARI SINI ===
                // (sesuai permintaanmu untuk skip fitur kunjungan)
            ])

            // === MIDDLEWARE (DARI FILE-MU, INI PENTING) ===
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

