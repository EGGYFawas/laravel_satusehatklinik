<x-filament-panels::page>
    <form wire:submit="save">
        
        <!-- Menampilkan Form Otomatis dari file PHP -->
        {{ $this->form }}

        <!-- Tombol Simpan -->
        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit">
                Simpan Perubahan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>