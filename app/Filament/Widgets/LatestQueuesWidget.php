<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\ClinicQueue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class LatestQueuesWidget extends BaseWidget
{
    protected static bool $isLazy = true;
    protected static ?int $sort = 3; 
    protected int | string | array $columnSpan = 'full'; 

    public function getTableHeading(): string
    {
        return 'Aktivitas Antrean Klinik Terkini (5 Terakhir)';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ClinicQueue::query()
                    ->orderBy('registration_time', 'desc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('patient.full_name')
                    ->label('Nama Pasien')
                    ->weight('medium') // [MODIFIKASI] Buat nama lebih tebal
                    ->searchable(),
                TextColumn::make('doctor.user.full_name')
                    ->label('Dokter Dituju')
                    ->searchable(),
                TextColumn::make('poli.name')
                    ->label('Poli')
                    ->badge()
                    ->color('primary'), // [MODIFIKASI] Beri warna badge
                TextColumn::make('queue_number')
                    ->label('No. Antrean')
                    ->weight('bold'), // [MODIFIKASI] Buat No. Antrean menonjol
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'MENUNGGU',
                        'info' => 'DIPANGGIL',
                        'primary' => 'DIPERIKSA',
                        'success' => 'SELESAI',
                        'danger' => 'BATAL',
                    ]),
            ]);
    }
}
