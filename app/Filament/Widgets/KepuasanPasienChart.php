<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
// Ganti Kunjungan::class dengan model-mu yang punya data kepuasan
use App\Models\Kunjungan; 
use Illuminate\Support\Facades\DB;

class KepuasanPasienChart extends ChartWidget
{
    protected static ?string $heading = 'Rata-rata Kepuasan Pasien (7 Hari Terakhir)';
    
    // Atur tinggi chart
    protected static ?string $maxHeight = '300px';

    // Atur refresh interval (misal: setiap 5 menit)
    protected static ?string $pollingInterval = '5m';

    protected function getData(): array
    {
        // Pastikan tabel Kunjungan punya kolom 'rating_kepuasan' (angka 1-5)
        // dan 'created_at'
        $data = Kunjungan::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('AVG(rating_kepuasan) as avg_rating')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('rating_kepuasan') // Hanya ambil yang ada rating
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->get();

        // Format data untuk chart
        $labels = $data->pluck('tanggal')->map(function ($date) {
            return date('d M', strtotime($date)); // Format label (misal: "29 Oct")
        })->toArray();
        
        $dataset = $data->pluck('avg_rating')->map(function ($rating) {
            return round($rating, 2); // Bulatkan AVG jadi 2 desimal
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Rata-rata Kepuasan (skala 1-5)',
                    'data' => $dataset,
                    'backgroundColor' => 'rgba(79, 70, 229, 0.2)', // Warna primary-mu
                    'borderColor' => '#4F46E5', // Warna primary-mu
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Tampilkan sebagai Line Chart
    }
}

