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

// Impor Widget
use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\LatestQueuesWidget; 
use App\Filament\Widgets\LatestPharmacyQueuesWidget;
use App\Filament\Widgets\ServiceTimeStatsWidget;
use App\Filament\Widgets\ClinicVisitsChart;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin') 
            ->login(false) 
            
            // === MODIFIKASI BRANDING & TEMA ===
            ->brandLogo(asset('assets/img/logo_login.png'))
            ->brandLogoHeight('5rem') // Logo lebih besar
            ->font('Poppins')
            ->colors([
                'primary' => '#4F46E5', // Warna primer dari referensi
                'gray' => Color::Slate, // Warna gray yang lebih formal
            ])
            // === AKHIR MODIFIKASI BRANDING ===

            // === [FIXED] FITUR BARU: SIDEBAR BUKA-TUTUP ===
            ->sidebarCollapsibleOnDesktop(true) 
            ->sidebarWidth('18rem') // Lebar sidebar (opsional)
            // === AKHIR PERBAIKAN ===

            // === [PERBAIKAN ERROR] FITUR BARU: TOMBOL LOGOUT ===
            ->profile(isSimple: false) // Menampilkan dropdown user lengkap
            
            // === [DIHAPUS] SEMUA BLOK userMenuItems DIHAPUS ===
            
            // === [PENGHAPUSAN STRATEGI C] ===
            // HAPUS baris ->viteTheme(...) dari sini
            // ->viteTheme('resources/css/filament-theme.css') 
            // === AKHIR PENGHAPUSAN ===
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            
            // [MODIFIKASI] Mendaftarkan widget yang sudah bersih
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                ServiceTimeStatsWidget::class, 
                DashboardStats::class, 
                ClinicVisitsChart::class,
                LatestQueuesWidget::class, 
                LatestPharmacyQueuesWidget::class,
            ])

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