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

// [MODIFIKASI] Impor 2 widget BARU kita
use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\LatestQueuesWidget; // Widget Tabel Baru
use App\Filament\Widgets\LatestPharmacyQueuesWidget; // <-- TAMBAHKAN INI
use App\Filament\Widgets\ServiceTimeStatsWidget; // <-- TAMBAHKAN INI
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
            
            ->brandLogo(asset('assets/img/logo_login.png'))
            ->brandLogoHeight('3.5rem') 
            ->font('Poppins')
            ->colors([
                'primary' => '#4F46E5', 
                'gray' => Color::Slate, 
            ])
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            
            // [MODIFIKASI] Mendaftarkan widget yang sudah bersih
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                ServiceTimeStatsWidget::class, // <-- TAMBAHKAN INI (Tampilkan di paling atas)
                DashboardStats::class, // Widget statistik kita yang sudah bersih
                ClinicVisitsChart::class,
                LatestQueuesWidget::class, // Widget tabel baru (Fase 1)
                LatestPharmacyQueuesWidget::class, // <-- TAMBAHKAN INI
                
                // 'KepuasanPasienChart' sudah resmi dihapus
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
