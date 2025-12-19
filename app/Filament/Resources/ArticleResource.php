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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

// Komponen Form
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload; // [BARU] Untuk upload gambar
use Filament\Forms\Components\Grid;       // [BARU] Untuk layout kolom
use Filament\Forms\Components\Group;      // [BARU] Untuk grouping layout
use Filament\Forms\Set;

// Komponen Tabel
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;  // [BARU] Untuk preview gambar di tabel

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationLabel = 'Artikel';
    protected static ?string $pluralModelLabel = 'Artikel';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Manajemen Konten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Menggunakan Grid untuk membagi layout menjadi 3 bagian (2 Kiri : 1 Kanan)
                Grid::make(3)
                    ->schema([
                        // === KOLOM KIRI (UTAMA) ===
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

                                        // RichEditor untuk Isi Konten
                                        RichEditor::make('content')
                                            ->label('Isi Artikel')
                                            ->required()
                                            // [PENTING] Konfigurasi agar gambar di dalam teks bisa diakses publik
                                            ->fileAttachmentsDirectory('articles/content') 
                                            ->fileAttachmentsVisibility('public')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // === KOLOM KANAN (SIDEBAR) ===
                        Group::make()
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Gambar Sampul')
                                    ->schema([
                                        // [PENTING] Upload Gambar Sampul
                                        FileUpload::make('image_url')
                                            ->label('Gambar Utama')
                                            ->image()             // Validasi file harus gambar
                                            ->imageEditor()       // Fitur Crop/Rotate bawaan Filament
                                            ->directory('articles/covers') // Folder penyimpanan
                                            ->visibility('public')         // Agar bisa dilihat Guest
                                            ->maxSize(2048)                // Maksimal 2MB (Mencegah loading lambat)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Pengaturan Publikasi')
                                    ->schema([
                                        Select::make('author_id')
                                            ->label('Penulis')
                                            ->relationship('author', 'name') // Pastikan 'name' atau 'full_name' ada di Model User
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
                // [BARU] Menampilkan thumbnail gambar di tabel list
                ImageColumn::make('image_url')
                    ->label('Cover')
                    ->circular() // Tampilan bulat rapi
                    ->defaultImageUrl(url('/images/placeholder.png')), // Opsional: gambar default

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Article $record): string => $record->title),

                TextColumn::make('author.name') // Sesuaikan dengan kolom nama di tabel users
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
                // Bisa tambahkan filter Draft/Published di sini nanti
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