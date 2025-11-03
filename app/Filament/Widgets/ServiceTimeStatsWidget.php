<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ClinicQueue;
use App\Models\PharmacyQueue;
use Illuminate\Support\Facades\Cache;

class ServiceTimeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1; 
    protected static bool $isLazy = true;

    private function formatSeconds($seconds): string
    {
        if ($seconds === null || $seconds < 0) return 'N/A';
        if ($seconds == 0) return '0 dtk';
        
        $m = floor($seconds / 60);
        $s = (int) $seconds % 60;
        
        return "{$m} mnt {$s} dtk";
    }

    protected function getStats(): array
    {
        $cacheKey = 'service_time_stats_today';
        $today = Carbon::today();

        $stats = Cache::remember($cacheKey, 600, function () use ($today) {
            
            $avgWaktuTungguPoli = ClinicQueue::query()
                ->whereDate('created_at', $today)
                ->whereNotNull('call_time')
                ->whereNotNull('registration_time') 
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, registration_time, call_time)) as avg_sec')
                ->value('avg_sec');

            $avgLamaPeriksa = ClinicQueue::query()
                ->whereDate('created_at', $today)
                ->where('status', 'SELESAI') 
                ->whereNotNull('finish_time')
                ->whereNotNull('call_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, call_time, finish_time)) as avg_sec')
                ->value('avg_sec');

            $avgTungguRacik = PharmacyQueue::query()
                ->whereDate('created_at', $today) 
                ->whereIn('status', ['SEDANG_DIRACIK', 'SIAP_DIAMBIL', 'DISERAHKAN', 'DITERIMA_PASIEN']) 
                ->whereNotNull('start_racik_time')
                ->whereNotNull('entry_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, entry_time, start_racik_time)) as avg_sec')
                ->value('avg_sec');

             $avgLamaRacik = PharmacyQueue::query()
                ->whereDate('created_at', $today)
                ->whereIn('status', ['SIAP_DIAMBIL', 'DISERAHKAN', 'DITERIMA_PASIEN']) 
                ->whereNotNull('finish_racik_time')
                ->whereNotNull('start_racik_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, start_racik_time, finish_racik_time)) as avg_sec')
                ->value('avg_sec');

             $avgTungguAmbil = PharmacyQueue::query()
                ->whereDate('created_at', $today)
                ->whereIn('status', ['DISERAHKAN', 'DITERIMA_PASIEN']) 
                ->whereNotNull('taken_time') 
                ->whereNotNull('finish_racik_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, finish_racik_time, taken_time)) as avg_sec')
                ->value('avg_sec');

            return [
                'avgWaktuTungguPoli' => $avgWaktuTungguPoli,
                'avgLamaPeriksa' => $avgLamaPeriksa,
                'avgTungguRacik' => $avgTungguRacik,
                'avgLamaRacik' => $avgLamaRacik,
                'avgTungguAmbil' => $avgTungguAmbil,
            ];
        }); 

        // --- [MODIFIKASI] Tampilkan 5 Kartu Stat DENGAN WARNA ---
        return [
            Stat::make('Rata-Rata Waktu Tunggu Poli (Hari Ini)', $this->formatSeconds($stats['avgWaktuTungguPoli']))
                ->description('Dari daftar s/d dipanggil')
                ->color('warning'), // Kuning untuk "menunggu"

            Stat::make('Rata-Rata Lama Pemeriksaan (Hari Ini)', $this->formatSeconds($stats['avgLamaPeriksa']))
                ->description('Dari dipanggil s/d selesai periksa')
                ->color('info'), // Biru untuk "durasi proses"

            Stat::make('Rata-Rata Tunggu Apotek (Hari Ini)', $this->formatSeconds($stats['avgTungguRacik']))
                ->description('Dari resep masuk s/d mulai diracik')
                ->color('warning'), // Kuning untuk "menunggu"

            Stat::make('Rata-Rata Lama Racik Obat (Hari Ini)', $this->formatSeconds($stats['avgLamaRacik']))
                ->description('Dari mulai s/d selesai diracik')
                ->color('info'), // Biru untuk "durasi proses"

            Stat::make('Rata-Rata Tunggu Ambil Obat (Hari Ini)', $this->formatSeconds($stats['avgTungguAmbil']))
                ->description('Dari selesai racik s/d diserahkan')
                ->color('danger'), // Merah untuk "pain point" terakhir pasien
        ];
    }
}
