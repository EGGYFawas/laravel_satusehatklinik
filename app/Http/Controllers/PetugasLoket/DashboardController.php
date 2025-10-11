<?php

namespace App\Http\Controllers\PetugasLoket;

use App\Http\Controllers\Controller;
use App\Models\PharmacyQueue;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama petugas loket/apotek.
     */
    public function index()
    {
        $today = Carbon::today();

        // [FIX] Daftar status yang relevan dan SINKRON dengan seluruh sistem
        $activeStatuses = ['MENUNGGU_RACIK', 'DIRACIK', 'SELESAI_RACIK', 'DIAMBIL'];
        
        $allQueues = PharmacyQueue::with([
                'clinicQueue.patient', 
                'prescription.prescriptionDetails.medicine'
            ])
            ->whereDate('entry_time', $today)
            ->whereIn('status', $activeStatuses) // <-- Perbaikan Kritis ada di sini
            ->orderBy('pharmacy_queue_number', 'asc')
            ->get();

        $queuesByStatus = $allQueues->groupBy('status');

        $menungguRacik = $queuesByStatus->get('MENUNGGU_RACIK', collect());
        $sedangDiracik = $queuesByStatus->get('DIRACIK', collect());
        $siapDiambil = $queuesByStatus->get('SELESAI_RACIK', collect());
        $telahDiserahkan = $queuesByStatus->get('DIAMBIL', collect());
        
        return view('petugas-loket.dashboard', compact(
            'menungguRacik',
            'sedangDiracik',
            'siapDiambil',
            'telahDiserahkan'
        ));
    }

    /**
     * Mengubah status antrean menjadi 'DIRACIK'.
     */
    public function startRacik(PharmacyQueue $pharmacyQueue)
    {
        if ($pharmacyQueue->status !== 'MENUNGGU_RACIK') {
            return redirect()->back()->with('error', 'Status antrean ini sudah diproses.');
        }

        $pharmacyQueue->update([
            'status' => 'DIRACIK',
            'start_racik_time' => now(),
        ]);

        return redirect()->route('petugas-loket.dashboard')->with('success', "Obat untuk antrean {$pharmacyQueue->pharmacy_queue_number} mulai diracik.");
    }

    /**
     * Mengubah status antrean menjadi 'SELESAI_RACIK'.
     */
    public function finishRacik(PharmacyQueue $pharmacyQueue)
    {
        if ($pharmacyQueue->status !== 'DIRACIK') {
            return redirect()->back()->with('error', 'Status antrean ini tidak sedang dalam proses peracikan.');
        }

        $pharmacyQueue->update([
            'status' => 'SELESAI_RACIK',
            'finish_racik_time' => now(),
        ]);

        return redirect()->route('petugas-loket.dashboard')->with('success', "Obat untuk antrean {$pharmacyQueue->pharmacy_queue_number} telah siap diambil.");
    }

    /**
     * Mengubah status antrean menjadi 'DIAMBIL' (menunggu konfirmasi pasien).
     */
    public function markAsTaken(PharmacyQueue $pharmacyQueue)
    {
        if ($pharmacyQueue->status !== 'SELESAI_RACIK') {
            return redirect()->back()->with('error', 'Obat untuk antrean ini belum siap diserahkan.');
        }

        $pharmacyQueue->update([
            'status' => 'DIAMBIL',
            'taken_time' => now(),
        ]);

        return redirect()->route('petugas-loket.dashboard')->with('success', "Obat untuk antrean {$pharmacyQueue->pharmacy_queue_number} telah diserahkan. Menunggu konfirmasi pasien.");
    }
}

