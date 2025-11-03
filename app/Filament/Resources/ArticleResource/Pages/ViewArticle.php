<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Actions;
// 1. GANTI 'view' menjadi 'ViewRecord'
use Filament\Resources\Pages\ViewRecord;

// 2. GANTI 'view' menjadi 'ViewRecord'
class ViewArticle extends ViewRecord
{
    protected static string $resource = ArticleResource::class;

    // Buat form read-only
    public function mount(int | string $record): void
    {
        parent::mount($record);
        $this->form->disabled();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            // Tombol kembali kustom
            Actions\Action::make('backToIndex')
                ->label('Kembali ke Daftar')
                ->url(ArticleResource::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}

