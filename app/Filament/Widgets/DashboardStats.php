<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Patient;
use App\Models\ClinicQueue; 
use App\Models\Doctor;

class DashboardStats extends BaseWidget
{
    protected static bool $isLazy = true;
    protected static ?int $sort = 2; // Di bawah ServiceTimeStats

    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $totalPasien = Patient::count();
        $totalDokter = Doctor::count();
        $totalPetugas = User::role('petugas loket')->count(); 
        $kunjunganHariIni = ClinicQueue::whereDate('registration_time', today())->count();

        // [MODIFIKASI] Kodemu sudah bagus dan berwarna-warni!
        // Kita hanya pastikan warnanya konsisten dan formal.
        return [
            Stat::make('Total Pasien', $totalPasien)
                ->description('Semua pasien terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'), // Biru (Info)

            Stat::make('Total Dokter', $totalDokter)
                ->description('Dokter aktif')
                ->descriptionIcon('heroicon-o-user-group') 
                ->color('success'), // Hijau (Success)

            Stat::make('Total Petugas', $totalPetugas)
                ->description('Petugas loket terdaftar')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('warning'), // Kuning (Warning)

            Stat::make('Kunjungan Hari Ini', $kunjunganHariIni)
                ->description('Antrean klinik hari ini')
                ->descriptionIcon('heroicon-o-queue-list') 
                ->color('primary'), // Biru Primary (sesuai tema)
        ];
    }
}

