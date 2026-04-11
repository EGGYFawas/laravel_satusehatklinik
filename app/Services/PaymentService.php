<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\ClinicSetting;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected const JASA_KLINIK = 15000; 

    public function __construct()
    {
        // Ambil credential dari database (SaaS Way)
        $settings = ClinicSetting::first();

        if ($settings) {
            Config::$serverKey = $settings->midtrans_server_key;
            // Kita asumsikan jika server key ada 'Mid-server-' berarti sandbox (false)
            // Atau lo bisa tambah kolom is_production di migrasi jika ingin lebih dinamis
            Config::$isProduction = false; 
            Config::$isSanitized = true;
            Config::$is3ds = true;
        } else {
            Log::error('PaymentService: Clinic settings not found in database.');
        }
    }

    public function calculateTotal(Prescription $prescription)
    {
        $total = 0;
        $prescription->loadMissing('details.medicine');

        foreach ($prescription->details as $detail) {
            $hargaSatuan = $detail->medicine->price ?? 0; 
            $qty = $detail->quantity;
            $total += ($hargaSatuan * $qty);
        }

        $medicalRecord = $prescription->medicalRecord;
        if ($medicalRecord) {
            $medicalRecord->loadMissing('actions');
            foreach ($medicalRecord->actions as $action) {
                $total += $action->price;
            }
        }

        $total += self::JASA_KLINIK;
        $prescription->update(['total_price' => $total]);

        return $total;
    }

    public function getSnapToken(Prescription $prescription)
    {
        $prescription->loadMissing([
            'details.medicine', 
            'medicalRecord.patient.user',
            'medicalRecord.actions'
        ]);

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

        $medicalRecord = $prescription->medicalRecord;
        if ($medicalRecord && $medicalRecord->actions->count() > 0) {
            foreach ($medicalRecord->actions as $action) {
                $price = (int) $action->price;
                $itemDetails[] = [
                    'id' => 'ACT-' . $action->id,
                    'price' => $price,
                    'quantity' => 1,
                    'name' => substr($action->action_name, 0, 45),
                ];
                $realTotal += $price;
            }
        }
        
        $itemDetails[] = [
            'id' => 'JASA-LAYANAN',
            'price' => self::JASA_KLINIK,
            'quantity' => 1,
            'name' => 'Jasa Layanan Klinik',
        ];
        $realTotal += self::JASA_KLINIK;

        if ($prescription->total_price != $realTotal) {
            $prescription->update(['total_price' => $realTotal]);
        }

        $orderId = 'INV-' . $prescription->id . '-' . time();

        $customerDetails = [
            'first_name' => $prescription->medicalRecord->patient->full_name ?? 'Pasien',
            'email' => $prescription->medicalRecord->patient->user->email ?? 'pasien@klinik.com',
            'phone' => $prescription->medicalRecord->patient->phone_number ?? '08123456789',
        ];

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $realTotal,
            ],
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            'callbacks' => [
                'finish' => route('pasien.billing.index'),
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $prescription->update([
                'midtrans_snap_token' => $snapToken,
                'midtrans_booking_code' => $orderId
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

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
            } 
            else if ($transactionStatus == 'cancel' || $transactionStatus == 'expire') {
                 $prescription->update(['payment_status' => 'failed']); 
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Gagal Cek Status Midtrans: ' . $e->getMessage());
            return false;
        }
    }

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