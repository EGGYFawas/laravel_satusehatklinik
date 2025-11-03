<?php

namespace App\Filament\Resources\DoctorScheduleResource\Pages;

use App\Filament\Resources\DoctorScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDoctorSchedule extends ViewRecord
{
    protected static string $resource = DoctorScheduleResource::class;

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
                ->url(DoctorScheduleResource::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
