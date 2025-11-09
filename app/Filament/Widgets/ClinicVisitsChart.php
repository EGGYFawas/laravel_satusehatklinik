<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use App\Models\ClinicQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ClinicVisitsChart extends ChartWidget
{
    protected static ?string $heading = 'Kunjungan Klinik (7 Hari Terakhir)';
    protected static ?int $sort = 3; 

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $data = Cache::remember('clinic_visits_chart_7_days', 600, function () {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            
            return ClinicQueue::query()
                ->where('created_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(created_at) as tanggal'),
                    DB::raw('COUNT(*) as jumlah')
                )
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'ASC')
                ->get();
        });

        $labels = $data->pluck('tanggal')->map(function ($date) {
            return Carbon::parse($date)->format('d M'); 
        })->toArray();
        
        $dataset = $data->pluck('jumlah')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kunjungan',
                    'data' => $dataset,
                    // [MODIFIKASI] Warna gradasi yang lebih cantik untuk Bar Chart
                    'backgroundColor' => [
                        'rgba(79, 70, 229, 0.2)', // primary-200
                        'rgba(79, 70, 229, 0.3)',
                        'rgba(79, 70, 229, 0.4)',
                        'rgba(79, 70, 229, 0.5)',
                        'rgba(79, 70, 229, 0.6)',
                        'rgba(79, 70, 229, 0.7)',
                        'rgba(79, 70, 229, 0.8)', // primary-800
                    ],
                    'borderColor' => '#4F46E5', // Warna primary-mu
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; 
    }

    /**
     * [JAWABAN UNTUKMU]
     * Bro, kodemu ini SUDAH SEMPURNA.
     * Bagian 'plugins' -> 'tooltip' -> 'callbacks' di bawah ini
     * adalah "magic" yang kamu minta untuk menampilkan "angka pasti"
     * saat di-hover. Tidak perlu diubah!
     */
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0
                    ],
                ],
            ],
            // [MODIFIKASI] Kodemu sudah benar, ini hanya format ulang
            // KODE INI YANG MENJAWAB PERMINTAAN "ANGKA PASTI" SAAT HOVER
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'js:function(context) {
                            let label = "Total: ";
                            if (context.parsed.y !== null) {
                                label += context.parsed.y + " Kunjungan";
                            }
                            return label;
                        }',
                    ],
                ],
            ],
        ];
    }
}
