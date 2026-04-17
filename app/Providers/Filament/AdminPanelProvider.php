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
            // PERUBAHAN 1: Jika Anda menggunakan Breeze/UI custom, biarkan false.
            // Tapi JIKA ANDA INGIN PAKAI LOGIN BAWAAN FILAMENT, ini HARUS ->login()
            ->login() // <-- KITA AKTIFKAN SEMENTARA UNTUK MEMASTIKAN
            
            // PERUBAHAN 2: TEGASKAN GUARD
            ->authGuard('web') 

            // === MODIFIKASI BRANDING & TEMA ===
            ->brandLogo(asset('assets/img/logo_login.png'))
            ->brandLogoHeight('5rem') 
            ->font('Poppins')
            ->colors([
                'primary' => '#4F46E5', 
                'gray' => Color::Slate, 
            ])

            // === FITUR BARU: SIDEBAR BUKA-TUTUP ===
            ->sidebarCollapsibleOnDesktop(true) 
            ->sidebarWidth('18rem') 

            // === FITUR BARU: TOMBOL LOGOUT ===
            ->profile(isSimple: false) 
            
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