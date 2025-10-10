<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicQueue;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckInController extends Controller
{
    // Ganti dengan UUID unik untuk klinik Anda. Bisa di-generate dari online generator.
    private const CLINIC_UUID = 'a1b2c3d4-e5f6-7890-1234-567890abcdef';

    public function processCheckIn($clinic_uuid)
    {
        // 1. Validasi UUID Klinik
        if ($clinic_uuid !== self::CLINIC_UUID) {
            return redirect()->route('pasien.dashboard')->with('error', 'QR Code tidak valid atau kedaluwarsa.');
        }

        $user = Auth::user();
        $today = Carbon::today();

        // 2. Cari antrean aktif milik pasien
        $activeQueue = ClinicQueue::whereHas('patient', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDate('registration_time', $today)
            ->where('status', 'MENUNGGU') // Hanya yang masih menunggu
            ->first();

        // 3. Jika tidak ada antrean yang bisa di check-in
        if (!$activeQueue) {
            return redirect()->route('pasien.dashboard')->with('error', 'Anda tidak memiliki antrean aktif yang bisa di check-in saat ini.');
        }

        // 4. Update status antrean
        $activeQueue->status = 'HADIR';
        $activeQueue->check_in_time = now();
        $activeQueue->save();

        return redirect()->route('pasien.dashboard')->with('success', 'Check-in berhasil! Mohon tunggu giliran Anda dipanggil.');
    }
}
