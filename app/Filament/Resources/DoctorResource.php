<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Filament\Resources\DoctorResource\RelationManagers;
use App\Models\User; // <-- Model User (Sudah Benar)
use App\Models\Poli; // <-- Model Poli (Sudah Benar)
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash; 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
// TAMBAHKAN IMPORT INI UNTUK VALIDASI PASSWORD
use Illuminate\Validation\Rules\Password; 

class DoctorResource extends Resource
{
    // UBAH MODEL TARGET (Sudah Benar)
    protected static ?string $model = User::class;
    
    // Ubah label di navigasi (Sudah Benar)
    protected static ?string $navigationLabel = 'Data Dokter';
    protected static ?string $pluralModelLabel = 'Data Dokter';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    /**
     * Filter query agar HANYA mengambil user 'dokter'. (Sudah Benar)
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('roles', function ($query) {
            $query->where('name', 'dokter');
        });
    }

    /**
     * Form untuk membuat User dan Doctor (via relationship)
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Akun User Dokter')
                    ->description('Informasi ini akan digunakan untuk login.')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                             // TAMBAHAN: Mencegah browser auto-fill
                            ->autocomplete('off'),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(static fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(static fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(static fn (string $state): string => Hash::make($state))
                            
                            // --- INI PERUBAHAN SESUAI PERMINTAAN ANDA ---
                            // 1. Tambahkan validasi bawaan Laravel
                            ->rule(Password::min(6)->letters()->numbers()) 
                            ->validationAttribute('password')
                            // 2. Ganti helper text
                            ->helperText('Harus kombinasi huruf dan angka, minimal 6 karakter.')
                            // 3. Tambahkan ini agar password tidak terisi otomatis
                            ->autocomplete('new-password'), 
                            // --- AKHIR PERUBAHAN ---
                    ]),
                
                // Masukkan data model Doctor menggunakan relationship() (Sudah Benar)
                Section::make('Informasi Profil Dokter')
                    ->description('Detail spesifik untuk profil dokter.')
                    ->relationship('doctor') // <-- Nama relasi 'doctor()' di model User
                    ->schema([
                        // Ambil poli dari model Poli (Sudah Benar)
                        Select::make('poli_id')
                            ->label('Poli')
                            ->options(Poli::all()->pluck('name', 'id')) // Opsi 2 (lebih stabil)
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('specialization')
                            ->label('Spesialisasi')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('license_number')
                            ->label('Nomor Izin Praktek (SIP)')
                            ->required()
                            ->maxLength(100)
                            // Validasi unique di tabel doctors (Sudah Benar)
                            ->unique(table: 'doctors', column: 'license_number', ignoreRecord: true),
                    ]),
            ]);
    }

    /**
     * Tabel untuk menampilkan data User dan relasi doctor (Sudah Benar)
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Data dari model User
                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                
                // Data dari relasi Doctor
                TextColumn::make('doctor.specialization')
                    ->label('Spesialisasi')
                    ->searchable(),
                TextColumn::make('doctor.license_number')
                    ->label('No. Izin')
                    ->searchable(),
                
                // Data dari relasi Poli (via relasi Doctor)
                TextColumn::make('doctor.poli.name')
                    ->label('Poli')
                    ->sortable()
                    ->badge(), // Tampilkan sebagai badge
            ])
            ->filters([
                //
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'view' => Pages\ViewDoctor::route('/{record}'), 
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}

