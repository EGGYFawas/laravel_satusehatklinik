<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\User; // <-- 1. MODEL DIUBAH KE USER
use App\Models\Patient; // Dibutuhkan untuk validasi
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Komponen Form
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Hash; // Untuk Hashing Password
use Illuminate\Validation\Rules\Password; // Untuk Validasi Password

// Komponen Tabel
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class PatientResource extends Resource
{
    // 1. MODEL DIUBAH KE USER
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Data Pasien';
    protected static ?string $pluralModelLabel = 'Data Pasien';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    /**
     * 2. TAMBAHKAN FUNGSI INI
     * Filter semua query agar HANYA mengambil user dengan role 'pasien'.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('roles', function ($query) {
            $query->where('name', 'pasien');
        });
    }

    /**
     * 3. UBAH TOTAL FORM INI
     * Sinkron dengan AuthController/register.blade.php
     * Akan membuat User dan Patient (via relationship) sekaligus.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Akun User Pasien (Untuk Login)')
                    ->description('Data ini akan membuat akun login baru.')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->autocapitalize('words'), // Otomatis uppercase
                        
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(table: 'users', column: 'email', ignoreRecord: true)
                            ->autocomplete('off'),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            // Wajib di halaman 'create'
                            ->required(static fn (string $operation): bool => $operation === 'create')
                            // Jangan ambil value-nya saat 'edit' jika kosong
                            ->dehydrated(static fn (?string $state): bool => filled($state))
                            // Hash password saat disimpan
                            ->dehydrateStateUsing(static fn (string $state): string => Hash::make($state))
                            ->maxLength(255)
                            ->autocomplete('new-password')
                            // Aturan validasi
                            ->rule(Password::min(6)->mixedCase()->numbers())
                            ->validationAttribute('Password')
                            ->helperText('Hanya isi untuk mengganti password. Minimal 6 karakter, mengandung huruf besar, huruf kecil, dan angka.'),
                    ])->columns(2),
                
                // Masukkan data model Patient menggunakan relationship()
                Section::make('Informasi Profil Pasien (Sesuai KTP)')
                    ->description('Detail demografis untuk pasien.')
                    // Nama relasi 'patient()' di model User
                    ->relationship('patient') 
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK (Nomor Induk Kependudukan)')
                            ->required()
                            ->length(16)
                            ->numeric()
                            ->unique(table: 'patients', column: 'nik', ignoreRecord: true),
                        DatePicker::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->required(),
                        Radio::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'Laki-laki' => 'Laki-laki',
                                'Perempuan' => 'Perempuan',
                            ])
                            ->required(),
                        TextInput::make('phone_number')
                            ->label('Nomor Telepon (WhatsApp)')
                            ->tel(),
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Informasi Medis Awal (Opsional)')
                    ->relationship('patient')
                    ->schema([
                        Select::make('blood_type')
                            ->label('Golongan Darah')
                            ->options([ 'A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O' ]),
                        Textarea::make('known_allergies')
                            ->label('Riwayat Alergi (jika ada)')
                            ->rows(3),
                        Textarea::make('chronic_diseases')
                            ->label('Penyakit Kronis (jika ada)')
                            ->rows(3),
                    ])->columns(1),
            ]);
    }

    /**
     * 4. UBAH TOTAL TABEL INI
     * Tabel sekarang menampilkan data User dan relasi 'patient'
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name') // Dari tabel 'users'
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email') // Dari tabel 'users'
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('patient.nik') // Dari relasi 'patient'
                    ->label('NIK')
                    ->searchable(),
                TextColumn::make('patient.phone_number') // Dari relasi 'patient'
                    ->label('No. Telepon'),
            ])
            ->filters([
                // Filter berdasarkan gender di relasi 'patient'
                SelectFilter::make('patient.gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                // [PERMINTAAN 1] Tambahkan DeleteAction dengan Modal Kustom
                Tables\Actions\DeleteAction::make()
                    ->modalHeading(fn (User $record) => 'Hapus Pasien: ' . $record->full_name)
                    ->modalDescription('Apakah Anda yakin ingin menghapus akun pasien ini? Semua data terkait (profil pasien, rekam medis, dll.) akan ikut terhapus secara permanen. Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'view' => Pages\ViewPatient::route('/{record}'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }    
}

