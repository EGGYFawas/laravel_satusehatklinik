<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalActionResource\Pages;
use App\Models\MedicalAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class MedicalActionResource extends Resource
{
    protected static ?string $model = MedicalAction::class;

    // Label navigasi di sidebar
    protected static ?string $navigationLabel = 'Tindakan & Pemeriksaan';
    protected static ?string $modelLabel = 'Tindakan Medis';
    protected static ?string $pluralModelLabel = 'Data Tindakan Medis';
    
    // Icon yang relevan (Clipboard Medis)
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Tindakan / Pemeriksaan')
                    ->description('Masukan nama tindakan dan harga jasa yang akan dibebankan ke pasien.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Tindakan')
                            ->placeholder('Contoh: Cek Gula Darah, Nebulizer, Jahit Luka')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(), // Agar nama panjang bisa muat

                        TextInput::make('price')
                            ->label('Biaya / Harga')
                            ->prefix('Rp')
                            ->numeric() // Wajib angka
                            ->required()
                            ->default(0)
                            ->helperText('Harga jasa per satu kali tindakan.'),
                    ])
                    ->columns(2), // Layout 2 kolom
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Tindakan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('price')
                    ->label('Biaya')
                    ->money('IDR') // Format otomatis Rp xx.xxx,00
                    ->sortable()
                    ->color('success'), // Memberikan warna hijau pada harga

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tambahkan filter di sini jika perlu
            ])
            ->actions([
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
            'index' => Pages\ListMedicalActions::route('/'),
            'create' => Pages\CreateMedicalAction::route('/create'),
            'edit' => Pages\EditMedicalAction::route('/{record}/edit'),
        ];
    }
}