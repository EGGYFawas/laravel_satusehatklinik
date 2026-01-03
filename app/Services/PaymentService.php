<?php

namespace App\Services;

use App\Models\Prescription;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    // Definisikan harga jasa di satu tempat agar konsisten
    protected const JASA_KLINIK = 15000; 

    public function __construct()
    {
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
        
        $prescription->loadMissing('details.medicine');

        foreach ($prescription->details as $detail) {
            $hargaSatuan = $detail->medicine->price ?? 0; 
            $qty = $detail->quantity;
            $total += ($hargaSatuan * $qty);
        }

        // Gunakan konstanta agar harga sama
        $total += self::JASA_KLINIK;

        $prescription->update(['total_price' => $total]);

        return $total;
    }

    /**
     * Request Snap Token ke Midtrans (Smart Logic)
     */
    public function getSnapToken(Prescription $prescription)
    {
        $prescription->loadMissing(['details.medicine', 'medicalRecord.patient.user']);

        // 1. CEK DULU: Apakah sudah ada token & Order ID yang aktif?
        if ($prescription->midtrans_snap_token && $prescription->midtrans_booking_code) {
            try {
                // Cek status transaksi terakhir ke Midtrans
                $status = Transaction::status($prescription->midtrans_booking_code);
                
                // Jika statusnya pending (user belum bayar atau belum selesai), pakai token lama
                if ($status->transaction_status == 'pending') {
                    return $prescription->midtrans_snap_token;
                }
                
                // Jika sudah lunas (settlement/capture), update DB dan jangan kasih token (harusnya redirect)
                if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                    $this->updateSuccessStatus($prescription, 'midtrans');
                    return null; // Sudah lunas
                }

                // Jika status expire/cancel/deny, baru kita buat Order ID BARU di bawah..
            } catch (\Exception $e) {
                // Jika order ID tidak ditemukan di Midtrans (404), berarti aman untuk buat baru
            }
        }

        // --- MULAI GENERATE TOKEN BARU ---

        $itemDetails = [];
        $realTotal = 0;

        foreach ($prescription->details as $detail) {
            $price = (int) ($detail->medicine->price ?? 0);
            $qty = (int) $detail->quantity;
            
            $itemDetails[] = [
                'id' => 'OBAT-' . $detail->medicine_id,
                'price' => $price,
                'quantity' => $qty,
                'name' => substr($detail->medicine->name, 0, 45),
            ];
            $realTotal += ($price * $qty);
        }
        
        // Tambah Jasa Klinik (Ambil dari Constant)
        $itemDetails[] = [
            'id' => 'JASA-LAYANAN',
            'price' => self::JASA_KLINIK,
            'quantity' => 1,
            'name' => 'Jasa Layanan Klinik',
        ];
        $realTotal += self::JASA_KLINIK;

        // Sinkronisasi Total
        if ($prescription->total_price != $realTotal) {
            $prescription->update(['total_price' => $realTotal]);
        }

        // Generate Order ID
        // Format: INV-IDPRES-TIMESTAMP (Hanya dibuat jika yang lama expire/batal)
        $orderId = 'INV-' . $prescription->id . '-' . time();

        $customerDetails = [
            'first_name' => $prescription->medicalRecord->patient->full_name ?? 'Pasien',
            'email' => $prescription->medicalRecord->patient->user->email ?? 'pasien@klinik.com',
            // Coba ambil no hp user jika ada, fallback ke dummy
            'phone' => $prescription->medicalRecord->patient->phone_number ?? '08123456789',
        ];

        // [MODIFIKASI] Menambahkan callbacks agar redirect balik ke aplikasi kita
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $realTotal,
            ],
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            // CALLBACKS: Ini kunci solusinya
            'callbacks' => [
                'finish' => route('pasien.billing.index'), // Redirect sukses
                'unfinish' => route('pasien.billing.index'), // Redirect belum selesai
                'error' => route('pasien.billing.index'), // Redirect error
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            
            // Simpan token & Order ID baru
            $prescription->update([
                'midtrans_snap_token' => $snapToken,
                'midtrans_booking_code' => $orderId
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cek status pembayaran ke Midtrans
     */
    public function checkTransactionStatus(Prescription $prescription)
    {
        if (!$prescription->midtrans_booking_code) {
            return false;
        }

        try {
            $status = Transaction::status($prescription->midtrans_booking_code);
            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status ?? null;

            if ($transactionStatus == 'settlement' || ($transactionStatus == 'capture' && $fraudStatus == 'accept')) {
                $this->updateSuccessStatus($prescription, 'midtrans');
                return true;
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'expire') {
                 // Reset status biar user bisa coba bayar lagi (generate token baru nanti)
                 $prescription->update(['payment_status' => 'failed']); 
            }
            
            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    // Helper private untuk update status lunas
    private function updateSuccessStatus(Prescription $prescription, $method) {
        if ($prescription->payment_status != 'paid') {
            $prescription->update([
                'payment_status' => 'paid',
                'payment_method' => $method,
                'paid_at' => now()
            ]);
        }
    }
}