<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PoliResource\Pages;
use App\Filament\Resources\PoliResource\RelationManagers;
use App\Models\Poli;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Komponen Form
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;

// Komponen Tabel
use Filament\Tables\Columns\TextColumn;

class PoliResource extends Resource
{
    protected static ?string $model = Poli::class;

    protected static ?string $navigationLabel = 'Data Poli';
    protected static ?string $pluralModelLabel = 'Data Poli';
    protected static ?string $navigationIcon = 'heroicon-o-building-office'; // Icon untuk Poli

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Poli')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Poli')
                            ->required()
                            ->maxLength(100)
                            ->helperText('Contoh: Poli Gigi, Poli ...'),
                        TextInput::make('code')
                            ->label('Kode Poli')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->helperText('Contoh: GIG, UMU, JTG'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Poli')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Kode Poli')
                    ->searchable()
                    ->sortable()
                    ->badge(), // Tampilkan sebagai badge agar rapi
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
            'index' => Pages\ListPolis::route('/'),
            'create' => Pages\CreatePoli::route('/create'),
            'view' => Pages\ViewPoli::route('/{record}'),
            'edit' => Pages\EditPoli::route('/{record}/edit'),
        ];
    }    
}
