<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TokenHelper
{
    /**
     * Mendapatkan token akses dari cache atau API.
     *
     * @return string
     * @throws \Exception
     */
    public static function getAccessToken(): string
    {
        // Cek apakah token sudah ada di cache
        $tokenData = Cache::get('access_token');

        if ($tokenData && now()->lt($tokenData['expires_at'])) {
            return $tokenData['token'];
        }

        // Token tidak ada atau sudah kedaluwarsa, generate token baru
        $response = Http::asForm()->post(env('AUTH_URL') . '/accesstoken?grant_type=client_credentials', [
            'client_id' => config('services.satusehat.client_id'),
            'client_secret' => config('services.satusehat.client_secret'),
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to retrieve access token');
        }

        $token = $response->json()['access_token'];
        $expiresIn = $response->json()['expires_in']; // Waktu kedaluwarsa dalam detik

        // Simpan token ke cache
        Cache::put('access_token', [
            'token' => $token,
            'expires_at' => now()->addSeconds($expiresIn - 60), // Beri buffer 1 menit untuk menghindari kedaluwarsa mendadak
        ]);

        return $token;
    }
}
