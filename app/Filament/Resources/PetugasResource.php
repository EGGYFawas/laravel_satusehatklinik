<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PetugasResource\Pages;
use App\Filament\Resources\PetugasResource\RelationManagers;
use App\Models\User; // <-- 1. Target model User
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
// 2. Tambahkan import ini untuk validasi password
use Illuminate\Validation\Rules\Password; 

class PetugasResource extends Resource
{
    // 3. Atur model, label, dan ikon
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Data Petugas Loket';
    protected static ?string $pluralModelLabel = 'Data Petugas Loket';
    protected static ?string $navigationIcon = 'heroicon-o-identification';

    /**
     * 4. Filter query agar HANYA mengambil user 'petugas loket'.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('roles', function ($query) {
            $query->where('name', 'petugas loket'); // <-- Filter role 'petugas loket'
        });
    }

    /**
     * 5. Form HANYA berisi data User.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Akun Petugas Loket')
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
                            ->autocomplete('off'), // <-- Tambahan dari DoctorResource
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(static fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(static fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(static fn (string $state): string => Hash::make($state))
                            
                            // --- Logika validasi disalin dari DoctorResource ---
                            ->rule(Password::min(6)->letters()->numbers()) 
                            ->validationAttribute('password')
                            ->helperText('Harus kombinasi huruf dan angka, minimal 6 karakter.')
                            ->autocomplete('new-password'), 
                            // --- Akhir logika validasi ---
                    ]),
                
                // TIDAK ADA Section::make('Informasi Profil') karena petugas tidak punya
            ]);
    }

    /**
     * 6. Tabel HANYA menampilkan data User.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                // TIDAK ADA kolom relasi dokter
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

    /**
     * 7. Daftarkan semua halaman (termasuk view)
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPetugas::route('/'),
            'create' => Pages\CreatePetugas::route('/create'),
            'view' => Pages\ViewPetugas::route('/{record}'), // <-- Pastikan view ada
            'edit' => Pages\EditPetugas::route('/{record}/edit'),
        ];
    }
}

