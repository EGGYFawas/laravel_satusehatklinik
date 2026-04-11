<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="flex items-center gap-3 mt-6">
            <x-filament::button type="submit" size="lg">
                Simpan Perubahan
            </x-filament::button>
            
            <x-filament::button type="button" color="gray" variant="ghost" wire:click="mount">
                Reset Form
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>