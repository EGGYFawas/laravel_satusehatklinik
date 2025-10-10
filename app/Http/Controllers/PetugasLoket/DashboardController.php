<?php

namespace App\Http\Controllers\PetugasLoket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PharmacyQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama petugas loket/apotek.
     *
     * Method ini mengambil semua antrean apotek untuk hari ini dan mengelompokkannya
     * berdasarkan status untuk ditampilkan di view.
     */
    public function index()
    {
        $today = Carbon::today();

        // Mengambil semua antrean apotek yang belum selesai untuk hari ini
        $allQueues = PharmacyQueue::with([
                'clinicQueue.patient', // Eager load data pasien
                'prescription.prescriptionDetails.medicine' // Eager load detail resep & nama obat
            ])
            ->whereDate('entry_time', $today)
            ->where('status', '!=', 'SELESAI') // Hanya tampilkan yang masih aktif
            ->orderBy('pharmacy_queue_number', 'asc')
            ->get();

        // Mengelompokkan antrean berdasarkan statusnya
        $queuesByStatus = $allQueues->groupBy('status');

        // Menyiapkan variabel untuk dikirim ke view, pastikan ada walau kosong
        $menungguRacik = $queuesByStatus->get('MENUNGGU_RACIK', collect());
        $sedangDiracik = $queuesByStatus->get('SEDANG_DIRACIK', collect());
        $siapDiambil = $queuesByStatus->get('SIAP_DIAMBIL', collect());
        
        return view('petugas-loket.dashboard', compact(
            'menungguRacik',
            'sedangDiracik',
            'siapDiambil'
        ));
    }

    /**
     * Mengubah status antrean menjadi 'SEDANG_DIRACIK'.
     *
     * @param PharmacyQueue $pharmacyQueue
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startRacik(PharmacyQueue $pharmacyQueue)
    {
        // Validasi untuk memastikan statusnya benar sebelum diubah
        if ($pharmacyQueue->status !== 'MENUNGGU_RACIK') {
            return redirect()->back()->with('error', 'Status antrean ini sudah diproses.');
        }

        $pharmacyQueue->update([
            'status' => 'SEDANG_DIRACIK',
            'start_racik_time' => now(),
        ]);

        return redirect()->route('petugas-loket.dashboard')->with('success', "Obat untuk antrean {$pharmacyQueue->pharmacy_queue_number} mulai diracik.");
    }

    /**
     * Mengubah status antrean menjadi 'SIAP_DIAMBIL'.
     *
     * @param PharmacyQueue $pharmacyQueue
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finishRacik(PharmacyQueue $pharmacyQueue)
    {
        if ($pharmacyQueue->status !== 'SEDANG_DIRACIK') {
            return redirect()->back()->with('error', 'Status antrean ini tidak sedang dalam proses peracikan.');
        }

        $pharmacyQueue->update([
            'status' => 'SIAP_DIAMBIL',
            'finish_racik_time' => now(),
        ]);

        return redirect()->route('petugas-loket.dashboard')->with('success', "Obat untuk antrean {$pharmacyQueue->pharmacy_queue_number} telah siap diambil.");
    }

    /**
     * Mengubah status antrean menjadi 'SELESAI' (obat sudah diserahkan).
     *
     * @param PharmacyQueue $pharmacyQueue
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsTaken(PharmacyQueue $pharmacyQueue)
    {
        if ($pharmacyQueue->status !== 'SIAP_DIAMBIL') {
            return redirect()->back()->with('error', 'Obat untuk antrean ini belum siap diserahkan.');
        }

        $pharmacyQueue->update([
            'status' => 'SELESAI',
            'taken_time' => now(),
        ]);

        return redirect()->route('petugas-loket.dashboard')->with('success', "Obat untuk antrean {$pharmacyQueue->pharmacy_queue_number} telah diserahkan kepada pasien.");
    }
}

