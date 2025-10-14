<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicQueue;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckInController extends Controller
{
    // Pastikan UUID ini sama dengan isi dari QR code Tipe Teks Anda
    private const CLINIC_UUID = 'a1b2c3d4-e5f6-7890-1234-567890abcdef';

    /**
     * Memproses permintaan check-in dari AJAX.
     * Mengembalikan response dalam format JSON.
     */
    public function processCheckInAjax($clinic_uuid)
    {
        // 1. Validasi UUID Klinik dari hasil scan QR Code
        if ($clinic_uuid !== self::CLINIC_UUID) {
            // Kembalikan response JSON error, bukan redirect.
            return response()->json([
                'success' => false, 
                'message' => 'QR Code tidak valid atau kedaluwarsa.'
            ], 400); // 400 Bad Request
        }

        $user = Auth::user();
        $today = Carbon::today();

        // 2. Cari antrean aktif milik pasien yang sedang menunggu
        $activeQueue = ClinicQueue::whereHas('patient', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDate('registration_time', $today)
            ->where('status', 'MENUNGGU') // Hanya cari yang statusnya MENUNGGU
            ->first();

        // 3. Jika tidak ada antrean yang bisa di check-in
        if (!$activeQueue) {
            // Kembalikan response JSON error, bukan redirect.
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki antrean aktif yang bisa di check-in saat ini.'
            ], 404); // 404 Not Found
        }

        // 4. Update status antrean menjadi HADIR
        $activeQueue->status = 'HADIR';
        $activeQueue->check_in_time = now();
        $activeQueue->save();

        // 5. Kembalikan response JSON sukses, bukan redirect.
        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil! Mohon tunggu giliran Anda dipanggil.'
        ], 200); // 200 OK
    }
}

