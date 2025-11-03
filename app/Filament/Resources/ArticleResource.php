<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use App\Models\User; // <-- Import User
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Komponen Form
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor; // <-- 1. INI KOMPONEN "MS WORD"
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Set; // <-- Untuk auto-slug
use Illuminate\Support\Str; // <-- Untuk auto-slug

// Komponen Tabel
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationLabel = 'Artikel';
    protected static ?string $pluralModelLabel = 'Artikel';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Konten Artikel')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true) // <-- 2. Update saat user pindah field
                             // 3. Otomatis isi 'slug' berdasarkan 'title'
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))), 

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(Article::class, 'slug', ignoreRecord: true),
                        
                        Select::make('author_id')
                            ->label('Penulis')
                            ->relationship('author', 'full_name') // Asumsi User punya full_name
                            ->searchable()
                            ->preload()
                            ->default(auth()->id()) // 4. Otomatis pilih user yg login
                            ->required(),

                        DateTimePicker::make('published_at')
                            ->label('Waktu Publikasi')
                            ->helperText('Kosongkan jika ingin disimpan sebagai draft.'),
                    ]),
                
                Section::make('Isi')
                    ->schema([
                        // 5. INI DIA RICH EDITOR-NYA
                        RichEditor::make('content')
                            ->label('Isi Artikel')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author.full_name')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('published_at')
                    ->label('Dipublikasi')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    // 6. Tampilkan "Draft" jika belum dipublish
                    ->default('Draft'), 
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'view' => Pages\ViewArticle::route('/{record}'), // <-- Pastikan ini ada
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }    
}
