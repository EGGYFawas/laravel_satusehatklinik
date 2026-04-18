<?php

namespace App\Services;

use App\Models\Prescription;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    // Konstanta harga jasa agar konsisten di seluruh aplikasi
    // Jasa Klinik ini adalah biaya admin/pendaftaran, di luar tindakan dokter
    protected const JASA_KLINIK = 15000; 

    public function __construct()
    {
        // Set konfigurasi Midtrans dari .env
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Hitung total tagihan (Obat + Tindakan Medis + Jasa Klinik) dan update ke database.
     * Fungsi ini memastikan total_price di database selalu sinkron.
     */
    public function calculateTotal(Prescription $prescription)
    {
        $total = 0;
        
        // 1. Hitung Biaya Obat
        // Load relasi obat jika belum ada
        $prescription->loadMissing('details.medicine');

        foreach ($prescription->details as $detail) {
            $hargaSatuan = $detail->medicine->price ?? 0; 
            $qty = $detail->quantity;
            $total += ($hargaSatuan * $qty);
        }

        // 2. Hitung Biaya Tindakan Medis (Pemeriksaan Tambahan)
        // Load relasi ke MedicalRecord -> Actions
        $medicalRecord = $prescription->medicalRecord;
        
        if ($medicalRecord) {
            $medicalRecord->loadMissing('actions'); // Pastikan relasi actions terload
            
            foreach ($medicalRecord->actions as $action) {
                // Ambil price dari tabel transaksi (medical_record_actions), bukan master
                $total += $action->price;
            }
        }

        // 3. Tambah Biaya Jasa Klinik (Admin/Sarana)
        $total += self::JASA_KLINIK;

        // Simpan ke database
        $prescription->update(['total_price' => $total]);

        return $total;
    }

    /**
     * Request Snap Token ke Midtrans (Smart Logic for Android/Web)
     * Mengirimkan detail item lengkap (Obat + Tindakan)
     */
    public function getSnapToken(Prescription $prescription)
    {
        // Load semua relasi penting:
        // 1. Detail Obat
        // 2. Data Pasien & User (untuk customer details)
        // 3. Tindakan Medis (untuk item details tambahan)
        $prescription->loadMissing([
            'details.medicine', 
            'medicalRecord.patient.user',
            'medicalRecord.actions' // <-- PENTING: Load tindakan
        ]);

        // --- STRATEGI: SELALU BUAT ORDER ID BARU ---
        // Kita tidak menggunakan token lama. Setiap kali klik "Bayar", kita generate Order ID baru.
        
        // Inisialisasi variabel
        $itemDetails = [];
        $realTotal = 0; // Hitung ulang total di sini untuk validasi

        // 1. Susun Item Details (RINCIAN OBAT)
        foreach ($prescription->details as $detail) {
            $price = (int) ($detail->medicine->price ?? 0);
            $qty = (int) $detail->quantity;
            
            $itemDetails[] = [
                'id' => 'OBAT-' . $detail->medicine_id,
                'price' => $price,
                'quantity' => $qty,
                'name' => substr($detail->medicine->name, 0, 45), // Limit nama 45 char (Midtrans limit)
            ];
            $realTotal += ($price * $qty);
        }

        // 2. Susun Item Details (RINCIAN TINDAKAN MEDIS) - [BARU]
        $medicalRecord = $prescription->medicalRecord;
        if ($medicalRecord && $medicalRecord->actions->count() > 0) {
            foreach ($medicalRecord->actions as $action) {
                $price = (int) $action->price;
                
                $itemDetails[] = [
                    'id' => 'ACT-' . $action->id, // Prefix ACT untuk Action
                    'price' => $price,
                    'quantity' => 1, // Jasa/Tindakan biasanya qty 1
                    'name' => substr($action->action_name, 0, 45),
                ];
                $realTotal += $price;
            }
        }
        
        // 3. Tambah Jasa Klinik (Biaya Admin)
        $itemDetails[] = [
            'id' => 'JASA-LAYANAN',
            'price' => self::JASA_KLINIK,
            'quantity' => 1,
            'name' => 'Jasa Layanan Klinik',
        ];
        $realTotal += self::JASA_KLINIK;

        // 4. Update total di database biar sama persis dengan yang dikirim ke Midtrans
        if ($prescription->total_price != $realTotal) {
            $prescription->update(['total_price' => $realTotal]);
        }

        // 5. Buat Order ID Baru yang Unik
        // Format: INV-[ID_RESEP]-[TIMESTAMP]
        $orderId = 'INV-' . $prescription->id . '-' . time();

        // 6. Susun Data Customer
        $customerDetails = [
            'first_name' => $prescription->medicalRecord->patient->full_name ?? 'Pasien',
            'email' => $prescription->medicalRecord->patient->user->email ?? 'pasien@klinik.com',
            'phone' => $prescription->medicalRecord->patient->phone_number ?? '08123456789',
        ];

        // 7. Susun Payload Utama
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $realTotal,
            ],
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            
            // [PENTING UNTUK ANDROID/WEBVIEW]
            // Callbacks memberitahu Midtrans harus redirect kemana setelah selesai.
            'callbacks' => [
                'finish' => route('pasien.billing.index'), // Kembali ke daftar tagihan
            ]
        ];

        try {
            // Request ke Server Midtrans
            $snapToken = Snap::getSnapToken($params);
            
            // Simpan token & Order ID baru ke Database
            $prescription->update([
                'midtrans_snap_token' => $snapToken,
                'midtrans_booking_code' => $orderId
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            // Throw error agar Controller bisa menangkap pesan aslinya
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Cek status pembayaran ke Midtrans & Update Database jika lunas
     * Digunakan oleh tombol "Refresh Status"
     */
    public function checkTransactionStatus(Prescription $prescription)
    {
        // Pastikan ada Order ID
        if (!$prescription->midtrans_booking_code) {
            return false;
        }

        try {
            // Tembak API Status Midtrans
            $status = Transaction::status($prescription->midtrans_booking_code);
            
            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status ?? null;

            // Logika Status Midtrans (Settlement = Lunas, Capture = Lunas Kartu Kredit)
            if ($transactionStatus == 'settlement' || ($transactionStatus == 'capture' && $fraudStatus == 'accept')) {
                
                $this->updateSuccessStatus($prescription, 'midtrans');
                return true; // Berhasil lunas
            } 
            // Jika batal/expire, update status jadi failed agar user sadar
            else if ($transactionStatus == 'cancel' || $transactionStatus == 'expire') {
                 $prescription->update(['payment_status' => 'failed']); 
            }
            
            return false; // Belum lunas

        } catch (\Exception $e) {
            Log::error('Gagal Cek Status Midtrans: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper private untuk update status lunas di database
     */
    private function updateSuccessStatus(Prescription $prescription, $method) {
        // Hanya update jika belum lunas, biar timestamp tidak berubah-ubah
        if ($prescription->payment_status != 'paid') {
            $prescription->update([
                'payment_status' => 'paid',
                'payment_method' => $method,
                'paid_at' => now()
            ]);
        }
    }
}