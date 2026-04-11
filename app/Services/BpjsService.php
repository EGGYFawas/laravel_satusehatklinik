<?php

namespace App\Services;

use App\Models\ClinicSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BpjsService
{
    protected $consId;
    protected $secretKey;
    protected $userKey;
    protected $baseUrl;
    protected $serviceName;

    public function __construct()
    {
        // 1. Ambil credential dari Database (Diisi via Filament)
        $settings = ClinicSetting::first();
        
        $this->consId = $settings?->bpjs_cons_id;
        $this->secretKey = $settings?->bpjs_secret_key;
        $this->userKey = $settings?->bpjs_user_key;
        
        // Sesuaikan dengan URL API BPJS yang lo pakai (V-Claim atau P-Care)
        // Default ini contoh URL V-Claim / P-Care Sandbox
        $this->baseUrl = 'https://apijkn-dev.bpjs-kesehatan.go.id';
        $this->serviceName = 'vclaim-rest-dev'; // Ubah sesuai layanan: pcare-rest-dev / vclaim-rest dll
    }

    /**
     * Generate X-Timestamp
     */
    protected function getTimestamp()
    {
        date_default_timezone_set('UTC');
        return strval(time() - strtotime('1970-01-01 00:00:00'));
    }

    /**
     * Generate X-Signature (HMAC-SHA256)
     */
    protected function getSignature($timestamp)
    {
        $signature = hash_hmac('sha256', $this->consId . "&" . $timestamp, $this->secretKey, true);
        return base64_encode($signature);
    }

    /**
     * Generate Headers wajib API BPJS
     */
    protected function getHeaders()
    {
        $timestamp = $this->getTimestamp();
        $signature = $this->getSignature($timestamp);

        return [
            'X-cons-id'   => $this->consId,
            'X-timestamp' => $timestamp,
            'X-signature' => $signature,
            'user_key'    => $this->userKey,
            'Accept'      => 'application/json',
            'Content-Type'=> 'application/json; charset=utf-8'
        ];
    }

    /**
     * Fungsi Decrypt Response BPJS (AES-256-CBC + LZString)
     * Berdasarkan standar API V-Claim v2 / P-Care
     */
    public function decryptResponse($response, $timestamp)
    {
        try {
            $key = $this->consId . $this->secretKey . $timestamp;
            $encrypt_method = 'AES-256-CBC';

            // Hash key
            $key_hash = hex2bin(hash('sha256', $key));
            
            // IV - 16 bytes pertama dari key hash
            $iv = substr($key_hash, 0, 16);
            
            // Decrypt AES-256-CBC
            $output = openssl_decrypt(base64_decode($response), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);

            // Decompress LZ-String
            // Catatan: Pastikan lo punya fungsi decompressLZString atau menggunakan library
            $decompressed = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

            return json_decode($decompressed, true);

        } catch (\Exception $e) {
            Log::error('BPJS Decrypt Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * FUNGSI UTAMA: Cek Kepesertaan by NIK
     * Endpoint V-Claim: /Peserta/nik/{nik}/tglSEP/{tglSEP}
     */
    public function getPesertaByNIK($nik)
    {
        if (!$this->consId || !$this->secretKey || !$this->userKey) {
            return ['success' => false, 'message' => 'Kredensial BPJS belum dikonfigurasi di Pengaturan Klinik.'];
        }

        try {
            $timestamp = $this->getTimestamp();
            $headers = $this->getHeaders();
            $tglSep = date('Y-m-d'); // Tanggal hari ini

            $endpoint = $this->baseUrl . '/' . $this->serviceName . '/Peserta/nik/' . $nik . '/tglSEP/' . $tglSep;

            $response = Http::withHeaders($headers)->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                
                // Cek apakah response sukses (Kode 200)
                if ($data['metaData']['code'] == '200') {
                    // Decrypt Data (Jika API BPJS yang lo pakai terenkripsi)
                    $decryptedData = $this->decryptResponse($data['response'], $timestamp);
                    
                    return [
                        'success' => true,
                        'data' => $decryptedData['peserta'] ?? $decryptedData
                    ];
                }

                return ['success' => false, 'message' => $data['metaData']['message']];
            }

            return ['success' => false, 'message' => 'Error API BPJS: ' . $response->status()];

        } catch (\Exception $e) {
            Log::error('BPJS API Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal terhubung ke server BPJS.'];
        }
    }
}