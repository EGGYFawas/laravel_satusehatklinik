<?php

namespace App\Filament\Resources;

// 1. Sesuaikan namespace ke 'ObatResource'
use App\Filament\Resources\ObatResource\Pages; 
use App\Filament\Resources\ObatResource\RelationManagers;
use App\Models\Medicine; // <-- Model tetap 'Medicine'
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Komponen Form
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;

// Komponen Tabel
use Filament\Tables\Columns\TextColumn;

// 2. Ubah nama class
class ObatResource extends Resource 
{
    // 3. Model tetap 'Medicine'
    protected static ?string $model = Medicine::class; 

    protected static ?string $navigationLabel = 'Data Obat';
    protected static ?string $pluralModelLabel = 'Data Obat';
    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Obat')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Obat')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('sku')
                            ->label('SKU (Kode Obat)')
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),
                        
                        Select::make('unit')
                            ->label('Satuan')
                            ->required()
                            ->options([
                                'Tablet' => 'Tablet',
                                'Kapsul' => 'Kapsul',
                                'Botol' => 'Botol',
                                'Strip' => 'Strip',
                                'Tube' => 'Tube',
                                'Ampul' => 'Ampul',
                            ])
                            ->searchable(),
                        
                        TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Obat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('unit')
                    ->label('Satuan')
                    ->badge(),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),
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
    
    // 4. Sesuaikan rute Pages
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObat::route('/'),
            'create' => Pages\CreateObat::route('/create'),
            'view' => Pages\ViewObat::route('/{record}'), 
            'edit' => Pages\EditObat::route('/{record}/edit'),
        ];
    }    
}
