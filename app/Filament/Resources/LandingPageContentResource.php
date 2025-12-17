<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingPageContentResource\Pages;
use App\Models\LandingPageContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Get; // Import penting untuk logika form dinamis

class LandingPageContentResource extends Resource
{
    protected static ?string $model = LandingPageContent::class;

    // Ikon di sidebar
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    
    // Label Sidebar
    protected static ?string $navigationLabel = 'Konten Landing Page';
    protected static ?string $modelLabel = 'Konten Landing Page';
    protected static ?string $navigationGroup = 'Pengaturan Web';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Edit Konten')
                    ->schema([
                        // 1. Label (Nama Bagian) - Read Only
                        TextInput::make('label')
                            ->label('Bagian Website')
                            ->disabled()
                            ->dehydrated() // Tetap kirim ke database saat save
                            ->required()
                            ->columnSpanFull(),

                        // 2. Key (Kode Sistem) - Hidden
                        TextInput::make('key')
                            ->hidden()
                            ->disabled()
                            ->dehydrated(false),

                        // 3. Input untuk Teks Pendek (Muncul jika type == text)
                        // Menggunakan dependency injection 'Get' yang lebih aman
                        TextInput::make('value')
                            ->label('Isi Konten')
                            ->hidden(fn (Get $get) => $get('type') !== 'text'),

                        // 4. Input untuk Teks Panjang (Muncul jika type == textarea)
                        Textarea::make('value')
                            ->label('Isi Deskripsi')
                            ->rows(5)
                            ->hidden(fn (Get $get) => $get('type') !== 'textarea'),

                        // 5. Input untuk Upload Gambar (Muncul jika type == image)
                        FileUpload::make('value')
                            ->label('Upload Gambar')
                            ->image()
                            ->disk('public') // Simpan di storage/app/public
                            ->directory('landing-page') // Folder tujuan
                            ->visibility('public')
                            ->imageEditor()
                            ->hidden(fn (Get $get) => $get('type') !== 'image'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Nama Bagian
                TextColumn::make('label')
                    ->label('Nama Bagian')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // Kolom Isi Konten
                TextColumn::make('value')
                    ->label('Isi Konten')
                    ->formatStateUsing(function ($state, $record) {
                        // Jika tipe datanya gambar, tampilkan info teks saja agar tabel rapi
                        if ($record->type === 'image') {
                            return '🖼️ [Gambar] (Klik Edit untuk melihat)';
                        }
                        // Jika teks biasa, batasi panjangnya
                        return \Illuminate\Support\Str::limit($state, 50);
                    }),

                // Badge Tipe Data
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'info',
                        'textarea' => 'warning',
                        'image' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->dateTime('d M Y, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Kosongkan agar admin tidak bisa hapus massal
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandingPageContents::route('/'),
            'edit' => Pages\EditLandingPageContent::route('/{record}/edit'),
        ];
    }

    // Matikan fitur Create & Delete agar data sistem aman
    public static function canCreate(): bool { return false; }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return false; }
}