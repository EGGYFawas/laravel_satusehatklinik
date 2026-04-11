<?php

namespace App\Filament\Pages;

use App\Models\ClinicSetting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ManageClinicSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan SIM Klinik';
    protected static ?string $title = 'Konfigurasi SaaS DistyMedic';
    protected static ?string $navigationGroup = 'Pengaturan Web';

    protected static string $view = 'filament.pages.manage-clinic-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = ClinicSetting::first();
        if ($settings) {
            $this->form->fill($settings->toArray());
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Profile & Landing Page')
                            ->icon('heroicon-m-home')
                            ->schema([
                                Section::make('Identitas Klinik')
                                    ->schema([
                                        TextInput::make('name')->required()->label('Nama Klinik'),
                                        TextInput::make('phone')->tel()->label('WhatsApp Gateway')->placeholder('08xxxxxxxxxx'),
                                        TextInput::make('email')->email()->label('Email Klinik'),
                                        TextInput::make('address')->label('Alamat Lengkap')->columnSpanFull(),
                                        FileUpload::make('logo')->image()->directory('clinic')->label('Logo Klinik'),
                                    ])->columns(2),
                                
                                Section::make('Tema & Warna (Custom SaaS)')
                                    ->schema([
                                        ColorPicker::make('primary_color')->label('Warna Utama Tema (Primary)')->placeholder('#2563eb'),
                                        ColorPicker::make('secondary_color')->label('Warna Latar/Aksen (Secondary)')->placeholder('#eff6ff'),
                                    ])->columns(2),

                                Section::make('Konten Gambar & Teks (Landing Page)')
                                    ->schema([
                                        // Header
                                        TextInput::make('hero_title')->label('Judul Utama (Hero)'),
                                        FileUpload::make('hero_image')->image()->directory('landing')->label('Gambar Header Utama'),
                                        
                                        // Mengapa Kami
                                        FileUpload::make('why_us_image')->image()->directory('landing')->label('Background "Mengapa Kami"')->columnSpanFull(),

                                        // Tentang Kami
                                        TextInput::make('about_us_title')->label('Judul Tentang Kami')->columnSpanFull(),
                                        Textarea::make('about_us_description')
                                            ->label('Deskripsi Tentang Kami')
                                            ->rows(4)
                                            ->columnSpanFull()
                                            ->helperText('Gunakan tombol Enter untuk membuat paragraf baru.'),
                                        FileUpload::make('about_us_image')->image()->directory('landing')->label('Gambar "Tentang Kami"')->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Payment Gateway')
                            ->icon('heroicon-m-credit-card')
                            ->schema([
                                Section::make('Midtrans Configuration')->schema([
                                    TextInput::make('midtrans_server_key')->password()->revealable()->label('Server Key'),
                                    TextInput::make('midtrans_client_key')->label('Client Key'),
                                ]),
                            ]),

                        Tabs\Tab::make('SatuSehat')
                            ->icon('heroicon-m-check-badge')
                            ->schema([
                                Section::make('Kemenkes Sandbox/Production')->schema([
                                    TextInput::make('satusehat_client_id')->label('Client ID'),
                                    TextInput::make('satusehat_client_secret')->password()->revealable()->label('Client Secret'),
                                    TextInput::make('satusehat_organization_id')->label('Organization ID'),
                                ]),
                            ]),

                        Tabs\Tab::make('BPJS Bridging')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Section::make('Credential V-Claim / P-Care')->schema([
                                    TextInput::make('bpjs_cons_id')->label('Consumer ID'),
                                    TextInput::make('bpjs_secret_key')->password()->revealable()->label('Secret Key'),
                                    TextInput::make('bpjs_user_key')->password()->revealable()->label('User Key'),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')->label('Simpan Perubahan')->action('save')->color('primary'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $settings = ClinicSetting::first() ?: new ClinicSetting();
            $settings->fill($data);
            $settings->save();

            Notification::make()->title('Berhasil!')->body('Konfigurasi klinik telah diperbarui.')->success()->send();
        } catch (\Exception $e) {
            Notification::make()->title('Gagal Menyimpan')->body('Terjadi kesalahan: ' . $e->getMessage())->danger()->send();
        }
    }
}