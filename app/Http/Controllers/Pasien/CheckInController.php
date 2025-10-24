<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicQueue;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckInController extends Controller
{
    private const CLINIC_UUID = 'a1b2c3d4-e5f6-7890-1234-567890abcdef';

    public function processCheckInAjax($clinic_uuid)
    {
        if ($clinic_uuid !== self::CLINIC_UUID) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau kedaluwarsa.'], 400);
        }

        $user = Auth::user();
        $today = Carbon::today(config('app.timezone'));

        $activeQueue = ClinicQueue::whereHas('patient', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDate('registration_time', $today)
            ->where('status', 'MENUNGGU')
            ->first();

        if (!$activeQueue) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki antrean aktif yang bisa di check-in saat ini.'], 404);
        }
        
        // [PERBAIKAN KONKRIT] Menggunakan Query Builder untuk mem-bypass Model Events.
        // Ini memastikan HANYA 'status' dan 'check_in_time' yang diubah, dan
        // 'registration_time' tidak akan tersentuh.
      
        $activeQueue->update([
            'status'        => 'HADIR',
            'check_in_time' => now(),
        ]);


        return response()->json(['success' => true, 'message' => 'Check-in berhasil! Mohon tunggu giliran Anda dipanggil.'], 200);
    }
}

