<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ClinicQueue;
use App\Models\PharmacyQueue;
// [DIHAPUS] Tidak perlu Impor Form
use Illuminate\Support\Facades\Cache;

// [DIHAPUS] Tidak perlu 'implements HasForms'
class ServiceTimeStatsWidget extends BaseWidget
{
    // [DIHAPUS] Tidak perlu 'use InteractsWithForms'

    protected static ?int $sort = 1; // Tampilkan di paling atas

    // [OPTIMASI] Kita tetap pakai Lazy Loading
    protected static bool $isLazy = true;

    // [DIHAPUS] Semua kode filter (property $filters, mount(), getFormSchema(), getFiltersForm())
    // sudah dihapus total.

    /**
     * Helper untuk memformat detik menjadi "X mnt Y dtk"
     */
    private function formatSeconds($seconds): string
    {
        if ($seconds === null || $seconds < 0) return 'N/A';
        if ($seconds == 0) return '0 dtk';
        
        $m = floor($seconds / 60);
        $s = (int) $seconds % 60;
        
        return "{$m} mnt {$s} dtk";
    }

    // [DIHAPUS] Tidak perlu method applyDateFilter()

    protected function getStats(): array
    {
        // [MODIFIKASI] Filter 'Hari Ini' di-hardcode di sini.
        $cacheKey = 'service_time_stats_today';
        $today = Carbon::today();

        // Simpan hasil query berat ini di cache selama 10 menit (600 detik)
        $stats = Cache::remember($cacheKey, 600, function () use ($today) {
            
            $avgWaktuTungguPoli = ClinicQueue::query()
                ->whereDate('created_at', $today) // <-- HARDCODED
                ->whereNotNull('call_time')
                ->whereNotNull('registration_time') 
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, registration_time, call_time)) as avg_sec')
                ->value('avg_sec');

            $avgLamaPeriksa = ClinicQueue::query()
                ->whereDate('created_at', $today) // <-- HARDCODED
                ->where('status', 'SELESAI') 
                ->whereNotNull('finish_time')
                ->whereNotNull('call_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, call_time, finish_time)) as avg_sec')
                ->value('avg_sec');

            $avgTungguRacik = PharmacyQueue::query()
                ->whereDate('created_at', $today) // <-- HARDCODED
                ->whereIn('status', ['SEDANG_DIRACIK', 'SIAP_DIAMBIL', 'DISERAHKAN', 'DITERIMA_PASIEN']) 
                ->whereNotNull('start_racik_time')
                ->whereNotNull('entry_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, entry_time, start_racik_time)) as avg_sec')
                ->value('avg_sec');

             $avgLamaRacik = PharmacyQueue::query()
                ->whereDate('created_at', $today) // <-- HARDCODED
                ->whereIn('status', ['SIAP_DIAMBIL', 'DISERAHKAN', 'DITERIMA_PASIEN']) 
                ->whereNotNull('finish_racik_time')
                ->whereNotNull('start_racik_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, start_racik_time, finish_racik_time)) as avg_sec')
                ->value('avg_sec');

             $avgTungguAmbil = PharmacyQueue::query()
                ->whereDate('created_at', $today) // <-- HARDCODED
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
        }); // <-- Akhir dari Cache::remember()

        // --- Tampilkan 5 Kartu Stat dari data cache ---
        return [
            Stat::make('Rata-Rata Waktu Tunggu Poli (Hari Ini)', $this->formatSeconds($stats['avgWaktuTungguPoli']))
                ->description('Dari daftar s/d dipanggil'),
            Stat::make('Rata-Rata Lama Pemeriksaan (Hari Ini)', $this->formatSeconds($stats['avgLamaPeriksa']))
                ->description('Dari dipanggil s/d selesai periksa'),
            Stat::make('Rata-Rata Tunggu Apotek (Hari Ini)', $this->formatSeconds($stats['avgTungguRacik']))
                ->description('Dari resep masuk s/d mulai diracik'),
            Stat::make('Rata-Rata Lama Racik Obat (Hari Ini)', $this->formatSeconds($stats['avgLamaRacik']))
                ->description('Dari mulai s/d selesai diracik'),
            Stat::make('Rata-Rata Tunggu Ambil Obat (Hari Ini)', $this->formatSeconds($stats['avgTungguAmbil']))
                ->description('Dari selesai racik s/d diserahkan'),
        ];
    }
}

