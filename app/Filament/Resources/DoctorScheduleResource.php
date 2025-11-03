<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorScheduleResource\Pages;
use App\Filament\Resources\DoctorScheduleResource\RelationManagers;
use App\Models\DoctorSchedule;
use App\Models\Doctor; // <-- 1. Import model Doctor
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// 2. Import komponen form dan tabel yang akan kita gunakan
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;

class DoctorScheduleResource extends Resource
{
    protected static ?string $model = DoctorSchedule::class;

    protected static ?string $navigationLabel = 'Jadwal Dokter';
    protected static ?string $pluralModelLabel = 'Jadwal Dokter';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 3. Dropdown untuk memilih dokter
                Select::make('doctor_id')
                    ->label('Dokter')
                    ->required()
                    // 4. Ambil semua dokter, tampilkan nama & polinya
                    ->options(Doctor::with(['user', 'poli'])->get()->mapWithKeys(fn ($doctor) => [
                        $doctor->id => $doctor->user->full_name . ' (Poli: ' . $doctor->poli->name . ')'
                    ]))
                    ->searchable()
                    ->preload(),
                
                // 5. Dropdown untuk hari
                Select::make('day_of_week')
                    ->label('Hari')
                    ->required()
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                        'Minggu' => 'Minggu',
                    ])
                    ->searchable(),
                
                // 6. Input jam
                TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->required()
                    ->seconds(false) // Sembunyikan detik
                    ->displayFormat('H:i'),
                
                TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->required()
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->after('start_time'), // Validasi: harus setelah jam mulai

                // 7. Toggle status aktif
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // 8. KUNCI UTAMA: Kelompokkan berdasarkan nama dokter
            ->defaultGroup('doctor.user.full_name')
            ->columns([
                // 9. Tampilkan data relasi
                TextColumn::make('doctor.user.full_name')
                    ->label('Nama Dokter')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('doctor.poli.name')
                    ->label('Poli')
                    ->badge(),
                TextColumn::make('day_of_week')
                    ->label('Hari')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->time('H:i') // Format jam
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                // 10. Filter untuk memilih dokter spesifik
                SelectFilter::make('doctor')
                    ->label('Filter Dokter')
                    ->relationship('doctor', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Doctor $record) => $record->user->full_name . ' (' . $record->poli->name . ')')
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // 11. Opsi grouping
            ->groups([
                Group::make('doctor.user.full_name')
                    ->label('Nama Dokter'),
                Group::make('day_of_week')
                    ->label('Hari'),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctorSchedules::route('/'),
            'create' => Pages\CreateDoctorSchedule::route('/create'),
            // 12. Daftarkan halaman view
            'view' => Pages\ViewDoctorSchedule::route('/{record}'), 
            'edit' => Pages\EditDoctorSchedule::route('/{record}/edit'),
        ];
    }    
}
