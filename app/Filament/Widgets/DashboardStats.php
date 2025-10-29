<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
// Impor Model-mu
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User; // Ganti jika model petugas beda
// Ganti Kunjungan::class dengan model-mu yang mencatat kunjungan harian
use App\Models\Kunjungan; 

class DashboardStats extends BaseWidget
{
    // Atur refresh interval (misal: setiap 30 detik)
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Ambil inspirasi dari dashboard.blade.php lamamu
        return [
            Stat::make('Total Dokter', Doctor::count())
                ->description('Dokter aktif terdaftar')
                ->descriptionIcon('fa-solid fa-user-doctor')
                ->color('success'),
            
            Stat::make('Total Pasien', Patient::count())
                ->description('Pasien terdaftar di sistem')
                ->descriptionIcon('fa-solid fa-user-injured')
                ->color('info'),

            // Asumsi role 'petugas loket' pakai Spatie
            Stat::make('Total Petugas', User::whereHas('roles', fn($q) => $q->where('name', 'petugas loket'))->count()) 
                ->description('Petugas loket & admin')
                ->descriptionIcon('fa-solid fa-user-shield')
                ->color('warning'),

            Stat::make('Kunjungan Hari Ini', 
                Kunjungan::whereDate('created_at', today())->count()
            )
                ->description('Pasien berkunjung hari ini')
                ->descriptionIcon('fa-solid fa-chart-line')
                ->color('primary'),
        ];
    }
}

