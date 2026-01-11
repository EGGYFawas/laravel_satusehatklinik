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
    // === 1. KONFIGURASI HALAMAN ===
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Kelola Landing Page';
    protected static ?string $title = 'Pengaturan Landing Page';
    protected static ?string $navigationGroup = 'Pengaturan Web';

    protected static string $view = 'filament.pages.manage-landing-page';

    // Variabel penampung data form
    public ?array $data = [];

    // === 2. SAAT HALAMAN DIBUKA (MOUNT) ===
    public function mount(): void
    {
        // Ambil semua data konten dari database
        $contents = LandingPageContent::all();
        $formData = [];
        
        foreach ($contents as $item) {
            // Jika tipe image, ambil dari kolom 'image', jika text ambil dari 'value'
            if ($item->type === 'image') {
                $formData[$item->key] = $item->image;
            } else {
                $formData[$item->key] = $item->value;
            }
        }

        // Masukkan data ke form
        $this->form->fill($formData);
    }

    // === 3. STRUKTUR FORM (SCHEMA) ===
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        // --- TAB 1: HERO SECTION ---
                        Tabs\Tab::make('Hero Section')
                            ->icon('heroicon-m-home')
                            ->schema([
                                TextInput::make('hero_title')
                                    ->label('Judul Utama (Hero)')
                                    ->required(),
                                
                                FileUpload::make('hero_image')
                                    ->label('Gambar Background Utama')
                                    ->image()
                                    ->disk('public')
                                    ->directory('landing-page')
                                    ->visibility('public')
                                    ->imageEditor(), // Fitur crop/resize
                            ]),

                        // --- TAB 2: TENTANG KAMI ---
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

                        // --- TAB 3: KONTAK ---
                        Tabs\Tab::make('Kontak')
                            ->icon('heroicon-m-phone')
                            ->schema([
                                TextInput::make('contact_phone')
                                    ->label('Nomor Telepon')
                                    ->tel() // Set tipe input HTML ke 'tel'
                                    ->placeholder('08xxxxxxxxxx')
                                    ->maxLength(13) // Batasi maksimal 13 karakter
                                    ->minLength(10) // Batasi minimal 10 karakter
                                    // Script JS: Hapus karakter non-angka & potong jika > 13 digit
                                    ->extraInputAttributes([
                                        'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)",
                                        'pattern' => '[0-9]*'
                                    ])
                                    // Validasi Backend: Pastikan hanya angka
                                    ->regex('/^[0-9]+$/') 
                                
                            ]),
                    ])
                    ->columnSpanFull(), // Agar Tabs memenuhi lebar halaman
            ])
            ->statePath('data'); // Bind ke variabel $data
    }

    // === 4. PROSES PENYIMPANAN (SAVE) ===
    public function save(): void
    {
        // Ambil data yang ada di form
        $formData = $this->form->getState();

        foreach ($formData as $key => $value) {
            // Cari data di database berdasarkan 'key'
            $content = LandingPageContent::where('key', $key)->first();

            if ($content) {
                // Logika Simpan
                if ($content->type === 'image') {
                    // Hapus gambar lama jika ada gambar baru (dan gambar lama ada)
                    if ($content->image && $content->image !== $value) {
                        Storage::disk('public')->delete($content->image);
                    }
                    $content->update(['image' => $value]);
                } else {
                    $content->update(['value' => $value]);
                }
            }
        }

        // Tampilkan notifikasi
        Notification::make()
            ->title('Berhasil disimpan')
            ->success()
            ->send();
    }
}