<?php

namespace App\Services;

use App\Models\ClinicSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $token;
    protected $clinicName;

    public function __construct()
    {
        $setting = ClinicSetting::first();
        // Ambil token dari database (atau dari .env sebagai fallback jika di lokal)
        $this->token = $setting?->fonnte_token ?? env('FONNTE_TOKEN');
        $this->clinicName = $setting?->name ?? 'Klinik Sehat';
    }

    /**
     * Fungsi untuk mengirim pesan teks via WhatsApp Fonnte
     * * @param string $target Nomor HP tujuan (08xxx atau 628xxx)
     * @param string $message Isi pesan
     */
    public function sendMessage($target, $message)
    {
        if (empty($this->token)) {
            Log::warning('Fonnte Error: Token WA belum disetting di Admin.');
            return false;
        }

        if (empty($target)) {
            Log::warning('Fonnte Error: Nomor HP tujuan kosong.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Otomatis konversi 08 jadi 628
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] == true) {
                Log::info("WA Terkirim ke $target: " . substr($message, 0, 30) . "...");
                return true;
            }

            Log::error("Fonnte Failed: " . json_encode($result));
            return false;

        } catch (\Exception $e) {
            Log::error("Fonnte Exception: " . $e->getMessage());
            return false;
        }
    }
}