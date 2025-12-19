<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

// Komponen Form
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Set;

// Komponen Tabel
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationLabel = 'Artikel';
    protected static ?string $pluralModelLabel = 'Artikel';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Manajemen Konten';

    // [PERBAIKAN 2] Mengatur Urutan: Draft (NULL) paling atas, lalu Published Terbaru
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByRaw('published_at IS NULL DESC') // 1. Draft (Null) paling atas
            ->orderBy('published_at', 'desc');        // 2. Artikel terbaru paling atas
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->columnSpan(2)
                            ->schema([
                                Section::make('Konten Artikel')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                        TextInput::make('slug')
                                            ->label('Slug (URL)')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(Article::class, 'slug', ignoreRecord: true),

                                        RichEditor::make('content')
                                            ->label('Isi Artikel')
                                            ->required()
                                            ->fileAttachmentsDirectory('articles/content') 
                                            ->fileAttachmentsVisibility('public')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Group::make()
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Gambar Sampul')
                                    ->schema([
                                        FileUpload::make('image_url')
                                            ->label('Gambar Utama')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('articles/covers')
                                            ->visibility('public')
                                            ->maxSize(2048)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Pengaturan Publikasi')
                                    ->schema([
                                        Select::make('author_id')
                                            ->label('Penulis')
                                            ->relationship('author', 'full_name')
                                            ->searchable()
                                            ->preload()
                                            ->default(auth()->id())
                                            ->required(),

                                        DateTimePicker::make('published_at')
                                            ->label('Waktu Publikasi')
                                            ->helperText('Kosongkan jika masih Draft.'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Cover')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Article $record): string => $record->title),

                TextColumn::make('author.full_name')
                    ->label('Penulis')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('d M Y, H:i') : 'Draft')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('published')
                    ->query(fn (Builder $query) => $query->whereNotNull('published_at'))
                    ->label('Sudah Terbit'),
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
            'view' => Pages\ViewArticle::route('/{record}'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}