<?php

namespace App\Http\Controllers\PetugasLoket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PharmacyQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama petugas loket/apotek.
     * [MODIFIKASI] Mengambil data untuk 4 kolom Kanban baru.
     */
    public function index()
    {
        $today = Carbon::today();

        // Mengambil semua antrean apotek untuk hari ini dengan relasi yang dibutuhkan
        $allQueues = PharmacyQueue::with([
                'clinicQueue.patient', 
                'clinicQueue.poli',
                'prescription.prescriptionDetails.medicine'
            ])
            ->whereDate('entry_time', $today)
            ->orderBy('pharmacy_queue_number', 'asc')
            ->get();

        // Mengelompokkan antrean berdasarkan status baru
        $dalamAntrean = $allQueues->where('status', 'DALAM_ANTREAN');
        $sedangDiracik = $allQueues->where('status', 'SEDANG_DIRACIK');
        $siapDiambil = $allQueues->where('status', 'SIAP_DIAMBIL');
        
        // Kolom riwayat menampilkan status DISERAHKAN dan DITERIMA_PASIEN
        $telahDiserahkan = $allQueues->whereIn('status', ['DISERAHKAN', 'DITERIMA_PASIEN'])
                                   ->sortByDesc('taken_time'); // Urutkan berdasarkan waktu diserahkan

        return view('petugas-loket.dashboard', compact(
            'dalamAntrean',
            'sedangDiracik',
            'siapDiambil',
            'telahDiserahkan'
        ));
    }

    /**
     * [FUNGSI BARU] Meng-handle semua pembaruan status dari satu route.
     * Menggantikan startRacik(), finishRacik(), dan markAsTaken().
     *
     * @param Request $request
     * @param PharmacyQueue $pharmacyQueue
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, PharmacyQueue $pharmacyQueue)
    {
        $request->validate([
            'status' => 'required|in:SEDANG_DIRACIK,SIAP_DIAMBIL,DISERAHKAN,BATAL'
        ]);

        $newStatus = $request->input('status');
        $currentStatus = $pharmacyQueue->status;
        $updateData = ['status' => $newStatus];
        $successMessage = '';

        try {
            DB::beginTransaction();

            // Logika untuk mengisi timestamp berdasarkan status baru
            switch ($newStatus) {
                case 'SEDANG_DIRACIK':
                    if ($currentStatus !== 'DALAM_ANTREAN') {
                        return redirect()->back()->with('error', 'Hanya resep dalam antrean yang bisa diproses.');
                    }
                    $updateData['start_racik_time'] = now();
                    $successMessage = "Resep {$pharmacyQueue->pharmacy_queue_number} mulai disiapkan.";
                    break;

                case 'SIAP_DIAMBIL':
                    if ($currentStatus !== 'SEDANG_DIRACIK') {
                        return redirect()->back()->with('error', 'Hanya resep yang sedang disiapkan yang bisa diselesaikan.');
                    }
                    $updateData['finish_racik_time'] = now();
                    $successMessage = "Obat untuk {$pharmacyQueue->pharmacy_queue_number} telah siap diambil.";
                    break;

                case 'DISERAHKAN':
                    if ($currentStatus !== 'SIAP_DIAMBIL') {
                        return redirect()->back()->with('error', 'Hanya obat yang siap yang bisa diserahkan.');
                    }
                    $updateData['taken_time'] = now();
                    $successMessage = "Obat untuk {$pharmacyQueue->pharmacy_queue_number} telah diserahkan ke pasien.";
                    break;
                
                // Opsi untuk pembatalan jika diperlukan di masa depan
                case 'BATAL':
                    $successMessage = "Antrean {$pharmacyQueue->pharmacy_queue_number} telah dibatalkan.";
                    break;
            }

            $pharmacyQueue->update($updateData);

            DB::commit();

            return redirect()->route('petugas-loket.dashboard')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal update status antrean apotek: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui status antrean.');
        }
    }
}
