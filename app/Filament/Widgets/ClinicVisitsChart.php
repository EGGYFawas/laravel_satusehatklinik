<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use App\Models\ClinicQueue;
use Illuminate\Support\Facades\DB;
// [PERBAIKAN] Impor Fasad Cache untuk 'Cache::remember()'
use Illuminate\Support\Facades\Cache;

class ClinicVisitsChart extends ChartWidget
{
    protected static ?string $heading = 'Kunjungan Klinik (7 Hari Terakhir)';
    protected static ?int $sort = 3; // Atur urutan, misal setelah 2 Stats Widget

    // [OPTIMASI PILAR 2] LAZY LOADING
    // Halaman dashboard akan dimuat instan, widget ini akan menyusul.
    protected static bool $isLazy = true;

    protected function getData(): array
    {
        // [OPTIMASI PILAR 2] CACHING
        // Kita cache chart ini selama 10 menit (600 detik)
        $data = Cache::remember('clinic_visits_chart_7_days', 600, function () {
            // Ambil data 6 hari lalu + hari ini (total 7 hari)
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

        // Siapkan data untuk chart
        $labels = $data->pluck('tanggal')->map(function ($date) {
            return Carbon::parse($date)->format('d M'); // Format label (misal: "03 Nov")
        })->toArray();
        
        $dataset = $data->pluck('jumlah')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kunjungan',
                    'data' => $dataset,
                    'backgroundColor' => 'rgba(79, 70, 229, 0.5)', // Warna primary-mu (dibuat sedikit transparan)
                    'borderColor' => '#4F46E5', // Warna primary-mu
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        // [PERUBAHAN KUNCI] Diubah dari 'line' menjadi 'bar'
        return 'bar'; // Tampilkan sebagai Bar Chart (Diagram Batang)
    }

    /**
     * [PERBAIKAN] Menambahkan method ini untuk mengatur opsi Chart.js
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    // [PERBAIKAN Y-AXIS "KOCAK"]
                    // Memaksa sumbu Y untuk hanya menampilkan angka bulat.
                    // Tidak akan ada lagi 0.5, 1.5, dst.
                    'ticks' => [
                        'precision' => 0
                    ],
                ],
            ],
            // [BONUS] Ini akan membuat tooltip (saat di-hover) lebih jelas
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        // Mengubah "Jumlah Kunjungan: 1" menjadi "1 Kunjungan"
                        'label' => 'js:function(context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label = "Total: ";
                            }
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

