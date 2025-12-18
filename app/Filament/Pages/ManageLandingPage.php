<?php

namespace App\Filament\Pages;

use App\Models\LandingPageContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ManageLandingPage extends Page
{
    // Konfigurasi Menu Sidebar
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Kelola Landing Page';
    protected static ?string $title = 'Pengaturan Landing Page';
    protected static ?string $navigationGroup = 'Pengaturan Web';

    // Menghubungkan ke file tampilan (Blade)
    protected static string $view = 'filament.pages.manage-landing-page';

    // Variabel untuk menampung data form
    public ?array $data = [];

    public function mount(): void
    {
        // 1. Ambil data dari database saat halaman dibuka
        $contents = LandingPageContent::all();
        $formData = [];
        
        foreach ($contents as $item) {
            // Logika: Jika tipe gambar, ambil data dari kolom 'image'
            // Jika tipe teks, ambil data dari kolom 'value'
            if ($item->type === 'image') {
                $formData[$item->key] = $item->image;
            } else {
                $formData[$item->key] = $item->value;
            }
        }

        // Isi form dengan data tersebut
        $this->form->fill($formData);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        // === TAB 1: HERO SECTION ===
                        Tabs\Tab::make('Hero Section')
                            ->icon('heroicon-m-home')
                            ->schema([
                                TextInput::make('hero_title')
                                    ->label('Judul Utama (Hero)')
                                    ->required(),
                                
                                FileUpload::make('hero_image')
                                    ->label('Gambar Background Utama')
                                    ->image()
                                    ->disk('public') // Pastikan storage:link aman
                                    ->directory('landing-page')
                                    ->visibility('public')
                                    ->imageEditor(), // Fitur crop/resize
                            ]),

                        // === TAB 2: TENTANG KAMI ===
                        Tabs\Tab::make('Tentang Kami')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                TextInput::make('about_us_title')
                                    ->label('Judul Tentang Kami')
                                    ->required(),

                                FileUpload::make('about_us_image')
                                    ->label('Gambar Tentang Kami')
                                    ->image()
                                    ->disk('public')
                                    ->directory('landing-page')
                                    ->visibility('public')
                                    ->imageEditor(),
                            ]),

                        // === TAB 3: KONTAK ===
                        Tabs\Tab::make('Kontak')
                            ->icon('heroicon-m-phone')
                            ->schema([
                                TextInput::make('contact_phone')
                                    ->label('Nomor Telepon')
                                    ->required(),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data'); // Mengikat input form ke variabel $data
    }

    public function save(): void
    {
        // 1. Ambil data dari form
        $formData = $this->form->getState();

        // 2. Loop dan simpan ke database
        foreach ($formData as $key => $value) {
            // Cari data di database berdasarkan key
            $content = LandingPageContent::where('key', $key)->first();

            if ($content) {
                // Simpan sesuai tipe datanya
                if ($content->type === 'image') {
                    // Hapus gambar lama jika diganti (Optional)
                    if ($content->image && $content->image !== $value) {
                        Storage::disk('public')->delete($content->image);
                    }
                    // Simpan ke kolom 'image'
                    $content->update(['image' => $value]);
                } else {
                    // Simpan ke kolom 'value'
                    $content->update(['value' => $value]);
                }
            }
        }

        // 3. Tampilkan notifikasi sukses
        Notification::make()
            ->title('Berhasil disimpan')
            ->success()
            ->send();
    }
}