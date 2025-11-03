<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\PharmacyQueue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class LatestPharmacyQueuesWidget extends BaseWidget
{
    // [OPTIMASI PILAR 2] LAZY LOADING
    protected static bool $isLazy = true;
    
    protected static ?int $sort = 4; // Di paling bawah
    protected int | string | array $columnSpan = 'full'; 

    public function getTableHeading(): string
    {
        return 'Aktivitas Antrean Apotek Terkini (5 Terakhir)';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PharmacyQueue::query()
                    ->orderBy('entry_time', 'desc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('clinicQueue.patient.full_name')
                    ->label('Nama Pasien'),
                TextColumn::make('pharmacy_queue_number')
                    ->label('No. Antrean Apotek'),
                // Gunakan status migrasi TERBARU Anda
                BadgeColumn::make('status')
                    ->label('Status Resep')
                    ->colors([
                        'warning' => 'DALAM_ANTREAN',
                        'info' => 'SEDANG_DIRACIK',
                        'primary' => 'SIAP_DIAMBIL',
                        'success' => 'DISERAHKAN',
                        'success' => 'DITERIMA_PASIEN',
                        'danger' => 'BATAL',
                    ]),
                TextColumn::make('entry_time')
                    ->label('Resep Masuk')
                    ->since(),
            ]);
    }
}

