<?php

namespace App\Filament\Resources;

// Import Model yang kita perlukan
use App\Filament\Resources\DoctorScheduleResource\Pages;
use App\Models\DoctorSchedule;
use App\Models\Doctor;
use App\Models\User;

// Import komponen Filament
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DoctorScheduleResource extends Resource
{
    protected static ?string $model = DoctorSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    // (Opsional) Mengganti nama di sidebar
    protected static ?string $modelLabel = 'Jadwal Dokter';
    protected static ?string $pluralModelLabel = 'Jadwal Dokter';

    // (Opsional) Mengelompokkan di navigasi sidebar
    protected static ?string $navigationGroup = 'Manajemen Klinik';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Pilih Dokter
                // Ini akan mencari nama di relasi 'user'
                Forms\Components\Select::make('doctor_id')
                    ->label('Dokter')
                    ->options(
                        Doctor::with('user')->get()->pluck('user.name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                
                // 2. Pilih Hari (sesuai ENUM di migrasi Anda)
                Forms\Components\Select::make('day_of_week')
                    ->label('Hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                        'Minggu' => 'Minggu',
                    ])
                    ->required(),
                
                // 3. Jam Mulai
                Forms\Components\TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->required(),
                
                // 4. Jam Selesai
                Forms\Components\TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->required(),
                
                // 5. Status Aktif
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Tampilkan Nama Dokter (dari relasi)
                // Kita memberitahu Filament untuk melihat relasi 'doctor', lalu 'user', lalu ambil 'name'
                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->label('Nama Dokter')
                    ->searchable()
                    ->sortable(),
                
                // 2. Tampilkan Hari
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Hari')
                    ->searchable()
                    ->sortable(),
                
                // 3. Tampilkan Jam
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->time('H:i') // Format jam
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->time('H:i') // Format jam
                    ->sortable(),
                
                // 4. Tampilkan Status
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                
                // (Opsional) Tampilkan kapan dibuat, bisa disembunyikan
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // (Kita bisa tambahkan filter hari di sini nanti jika perlu)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Tambahkan aksi Hapus
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    // Filament sudah mengatur halaman-halaman ini secara otomatis
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctorSchedules::route('/'),
            'create' => Pages\CreateDoctorSchedule::route('/create'),
            'edit' => Pages\EditDoctorSchedule::route('/{record}/edit'),
        ];
    }    
}