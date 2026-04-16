<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\MedicalRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TopDiseasesChart extends ChartWidget
{
    protected static ?string $heading = '5 Penyakit Terbanyak (30 Hari Terakhir)';
    protected static ?int $sort = 5; // Posisi di bawah widget aktivitas
    protected int | string | array $columnSpan = 'full'; // Biar grafiknya lebar dan lega

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        // Cache selama 10 menit agar query ke database tidak jebol
        $data = Cache::remember('top_diseases_chart_30_days', 600, function () {
            $startDate = Carbon::now()->subDays(30)->startOfDay();
            
            // Mengambil dari tabel medical_records berdasarkan primary_icd10_name
            return MedicalRecord::query()
                ->whereNotNull('primary_icd10_name')
                ->where('created_at', '>=', $startDate)
                ->select(
                    'primary_icd10_name',
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('primary_icd10_name')
                ->orderBy('total', 'DESC')
                ->limit(5) // Ambil Top 5
                ->get();
        });

        // Potong nama penyakit jika terlalu panjang agar rapi di grafik
        $labels = $data->pluck('primary_icd10_name')->map(function ($name) {
            return strlen($name) > 35 ? substr($name, 0, 35) . '...' : $name;
        })->toArray();
        
        $dataset = $data->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kasus',
                    'data' => $dataset,
                    // Palet warna warni biar menarik untuk dilihat
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.7)',   // Merah (Red)
                        'rgba(245, 158, 11, 0.7)',  // Oranye (Amber)
                        'rgba(16, 185, 129, 0.7)',  // Hijau (Emerald)
                        'rgba(59, 130, 246, 0.7)',  // Biru (Blue)
                        'rgba(139, 92, 246, 0.7)',  // Ungu (Violet)
                    ],
                    'borderColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(16, 185, 129)',
                        'rgb(59, 130, 246)',
                        'rgb(139, 92, 246)',
                    ],
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; 
    }

    protected function getOptions(): array
    {
        return [
            // [Trik Rahasia] Jadikan Bar Chart berbentuk Horizontal
            'indexAxis' => 'y', 
            'scales' => [
                'x' => [ // Karena horizontal, nilai angkanya pindah ke sumbu X
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0 // Hindari angka desimal untuk jumlah orang
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false, // Sembunyikan tulisan "Jumlah Kasus" di atas karena warnanya sudah beda-beda
                ],
                'tooltip' => [
                    'callbacks' => [
                        // Karena grafiknya horizontal, kita baca nilainya dari context.parsed.x
                        'label' => 'js:function(context) {
                            let label = " Total: ";
                            if (context.parsed.x !== null) {
                                label += context.parsed.x + " Pasien";
                            }
                            return label;
                        }',
                    ],
                ],
            ],
        ];
    }
}