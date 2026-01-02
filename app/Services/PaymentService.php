<?php

namespace App\Services;

use App\Models\Prescription;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Hitung total tagihan dan update ke database
     */
    public function calculateTotal(Prescription $prescription)
    {
        $total = 0;
        
        // Load relasi jika belum ada
        $prescription->loadMissing('details.medicine');

        foreach ($prescription->details as $detail) {
            $hargaSatuan = $detail->medicine->price ?? 0; 
            $qty = $detail->quantity;
            $total += ($hargaSatuan * $qty);
        }

        // Biaya Jasa Klinik
        $jasaKlinik = 15000; 
        $total += $jasaKlinik;

        $prescription->update(['total_price' => $total]);

        return $total;
    }

    /**
     * Request Snap Token ke Midtrans
     */
    public function getSnapToken(Prescription $prescription)
    {
        // Load relasi penting
        $prescription->loadMissing(['details.medicine', 'medicalRecord.patient.user']);

        // 1. Susun Item Details (Obat)
        $itemDetails = [];
        $realTotal = 0; // Hitung ulang total di sini biar sinkron dengan item

        foreach ($prescription->details as $detail) {
            $price = (int) ($detail->medicine->price ?? 0);
            $qty = (int) $detail->quantity;
            
            $itemDetails[] = [
                'id' => 'OBAT-' . $detail->medicine_id,
                'price' => $price,
                'quantity' => $qty,
                'name' => substr($detail->medicine->name, 0, 45), // Limit nama biar aman
            ];
            $realTotal += ($price * $qty);
        }
        
        // 2. Tambah Jasa Klinik
        $jasaPrice = 15000;
        $itemDetails[] = [
            'id' => 'JASA-LAYANAN',
            'price' => $jasaPrice,
            'quantity' => 1,
            'name' => 'Jasa Layanan Klinik',
        ];
        $realTotal += $jasaPrice;

        // Update total di database biar sama persis
        if ($prescription->total_price != $realTotal) {
            $prescription->update(['total_price' => $realTotal]);
        }

        // Cek Token Lama (Validasi Expired Sederhana)
        // BAGIAN INI DI-KOMENTARI AGAR SELALU GENERATE ORDER ID BARU
        // Tujuannya agar user bisa ganti metode pembayaran jika sebelumnya batal/tutup popup
        /*
        if ($prescription->midtrans_snap_token && $prescription->payment_status == 'pending') {
            return $prescription->midtrans_snap_token;
        }
        */

        // 3. Buat Order ID Baru
        // Format: INV-[ID]-[TIMESTAMP] agar unik setiap kali klik bayar
        $orderId = 'INV-' . $prescription->id . '-' . time();

        // 4. Susun Payload
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $realTotal, // Gunakan total hasil penjumlahan item
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $prescription->medicalRecord->patient->full_name ?? 'Pasien',
                // Gunakan email dummy jika user tidak punya email, karena Midtrans butuh field ini valid
                'email' => $prescription->medicalRecord->patient->user->email ?? 'pasien@klinik.com', 
                'phone' => '08123456789', // Bisa diganti dengan no hp pasien jika ada
            ],
        ];

        try {
            // Request ke Midtrans
            $snapToken = Snap::getSnapToken($params);
            
            // Simpan token baru
            $prescription->update([
                'midtrans_snap_token' => $snapToken,
                'midtrans_booking_code' => $orderId
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            // Log error biar tau salahnya dimana (Cek storage/logs/laravel.log)
            Log::error('Midtrans Error: ' . $e->getMessage());
            // Optional: Throw ulang error agar bisa ditangkap controller untuk debugging
            // throw $e; 
            return null;
        }
    }

    /**
     * Cek status pembayaran ke Midtrans & Update Database jika lunas
     */
    public function checkTransactionStatus(Prescription $prescription)
    {
        // Pastikan ada Order ID
        if (!$prescription->midtrans_booking_code) {
            return false;
        }

        try {
            // Tembak API Status Midtrans
            $status = \Midtrans\Transaction::status($prescription->midtrans_booking_code);
            
            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status ?? null;

            // Logika Status Midtrans (Settlement = Lunas, Capture = Lunas Kartu Kredit)
            if ($transactionStatus == 'settlement' || ($transactionStatus == 'capture' && $fraudStatus == 'accept')) {
                
                // Update Database jadi PAID
                $prescription->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'midtrans', // Cashless
                    'paid_at' => now()
                ]);
                
                return true; // Berhasil update lunas
            }
            
            return false; // Belum lunas / pending / expire

        } catch (\Exception $e) {
            Log::error('Gagal Cek Status Midtrans: ' . $e->getMessage());
            return false;
        }
    }
}