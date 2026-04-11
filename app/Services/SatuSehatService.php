<?php

namespace App\Services;

use App\Models\ClinicSetting;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SatuSehatService
{
    protected $clientId;
    protected $clientSecret;
    protected $organizationId;
    protected $baseUrl;

    public function __construct()
    {
        // 1. Ambil credential dari Database (Pengaturan yang diisi Admin di Filament)
        $settings = ClinicSetting::first();
        
        $this->clientId = $settings?->satusehat_client_id;
        $this->clientSecret = $settings?->satusehat_client_secret;
        $this->organizationId = $settings?->satusehat_organization_id; // Penting untuk pengiriman rekam medis
        
        // Default menggunakan URL Sandbox Kemenkes (Untuk Testing)
        // Jika nanti production, URL ini diubah ke URL Production Kemenkes
        $this->baseUrl = 'https://api-satusehat-stg.dto.kemkes.go.id';
    }

    /**
     * Fungsi untuk mendapatkan Access Token dari Kemenkes.
     * Token ini di-cache selama 55 menit karena masa aktifnya 1 jam.
     */
    public function getAccessToken()
    {
        // Cek apakah token masih ada di cache
        if (Cache::has('satusehat_access_token')) {
            return Cache::get('satusehat_access_token');
        }

        if (!$this->clientId || !$this->clientSecret) {
            Log::error('SatuSehat Auth Error: Client ID atau Secret belum disetting di Admin.');
            return null;
        }

        try {
            // Hit API Kemenkes untuk minta Token
            $response = Http::asForm()->post($this->baseUrl . '/oauth2/v1/accesstoken?grant_type=client_credentials', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->successful()) {
                $token = $response->json('access_token');
                
                // Simpan token di memori (cache) selama 55 menit (3300 detik)
                Cache::put('satusehat_access_token', $token, 3300);
                
                return $token;
            }

            Log::error('SatuSehat Auth Failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('SatuSehat Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * FUNGSI 1: Pencarian Data Pasien berdasarkan NIK (KYC)
     * Digunakan saat Registrasi / Loket
     */
    public function getPatientByNIK($nik)
    {
        $token = $this->getAccessToken();
        if (!$token) return ['success' => false, 'message' => 'Gagal mendapatkan token Kemenkes.'];

        try {
            $response = Http::withToken($token)
                ->get($this->baseUrl . '/fhir-r4/v1/Patient', [
                    'identifier' => 'https://fhir.kemkes.go.id/id/nik|' . $nik
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Jika total data > 0, artinya NIK valid dan ditemukan di Kemenkes
                if (isset($data['total']) && $data['total'] > 0) {
                    $patientData = $data['entry'][0]['resource'];
                    return [
                        'success' => true,
                        'data' => [
                            'ihs_number' => $patientData['id'], // Ini ID SatuSehat (Wajib disimpan di DB)
                            'name' => $patientData['name'][0]['text'] ?? '',
                            'gender' => $patientData['gender'] ?? '',
                            'birthDate' => $patientData['birthDate'] ?? '',
                        ]
                    ];
                }
                
                return ['success' => false, 'message' => 'NIK tidak ditemukan di database Kemenkes.'];
            }

            return ['success' => false, 'message' => 'Error API Kemenkes: ' . $response->status()];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Koneksi API Error: ' . $e->getMessage()];
        }
    }

    /**
     * FUNGSI 2: Mengirim Data Rekam Medis (Encounter)
     * Digunakan saat Dokter menekan tombol "Selesaikan Pemeriksaan"
     */
    public function sendMedicalRecord(MedicalRecord $record)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Gagal mendapatkan token Kemenkes.'];
        }

        $patient = $record->patient;
        
        // Validasi: Pasien wajib punya IHS Number
        if (!$patient || empty($patient->ihs_number)) {
            return ['success' => false, 'message' => 'Gagal kirim: Pasien belum memiliki IHS Number (Belum validasi NIK).'];
        }

        // Validasi: Klinik wajib setting Organization ID
        if (!$this->organizationId) {
             return ['success' => false, 'message' => 'Organization ID Kemenkes belum diatur di Pengaturan Klinik.'];
        }

        // Ambil IHS Number Dokter (Jika kosong, gunakan string N/A untuk keperluan Sandbox/Testing)
        $doctorIhs = $record->doctor->user->ihs_number ?? 'N/A';

        // ---------------------------------------------------------
        // STANDAR FHIR R4: ENCOUNTER PAYLOAD
        // ---------------------------------------------------------
        $payload = [
            "resourceType" => "Encounter",
            "status" => "finished", // Karena status dikirim setelah pemeriksaan selesai
            "class" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                "code" => "AMB", // Ambulatory / Rawat Jalan
                "display" => "ambulatory"
            ],
            "subject" => [
                "reference" => "Patient/" . $patient->ihs_number,
                "display" => $patient->full_name
            ],
            "participant" => [
                [
                    "type" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                    "code" => "ATND",
                                    "display" => "attender"
                                ]
                            ]
                        ]
                    ],
                    "individual" => [
                        "reference" => "Practitioner/" . $doctorIhs,
                        "display" => $record->doctor->user->full_name ?? 'Dokter Pemeriksa'
                    ]
                ]
            ],
            "period" => [
                // Menggunakan timezone standar HL7 FHIR (e.g., 2026-04-10T10:00:00+07:00)
                "start" => $record->created_at->format('Y-m-d\TH:i:sP'),
                "end" => now()->format('Y-m-d\TH:i:sP')
            ],
            "diagnosis" => [
                [
                    "condition" => [
                        "display" => $record->primary_icd10_name . ' (' . $record->primary_icd10_code . ')'
                    ],
                    "use" => [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                "code" => "DD",
                                "display" => "Discharge diagnosis"
                            ]
                        ]
                    ],
                    "rank" => 1
                ]
            ],
            "serviceProvider" => [
                "reference" => "Organization/" . $this->organizationId
            ]
        ];

        try {
            // Tembak Data ke Kemenkes
            $response = Http::withToken($token)
                ->post($this->baseUrl . '/fhir-r4/v1/Encounter', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true, 
                    'encounter_id' => $data['id'] ?? null // Ini adalah ID Bukti dari Kemenkes
                ];
            }

            Log::error('SatuSehat Encounter Error: ' . $response->body());
            return ['success' => false, 'message' => 'Gagal dari server Kemenkes: ' . $response->body()];

        } catch (\Exception $e) {
            Log::error('SatuSehat Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Koneksi Error: ' . $e->getMessage()];
        }
    }
}