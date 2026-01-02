<?php

namespace App\Http\Controllers\PetugasLoket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PharmacyQueue;
use App\Services\PaymentService; // Import Service Pembayaran
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama petugas loket/apotek.
     */
    public function index()
    {
        $today = Carbon::today();

        // Mengambil semua antrean apotek untuk hari ini
        $allQueues = PharmacyQueue::with([
                'clinicQueue.patient', 
                'clinicQueue.poli',
                'prescription.prescriptionDetails.medicine' // Load detail obat & resep
            ])
            ->whereDate('entry_time', $today)
            ->orderBy('pharmacy_queue_number', 'asc')
            ->get();

        // --- TAMBAHAN LOGIC: Pastikan Total Harga Terhitung ---
        // Kita loop sekilas untuk trigger hitung total jika masih 0
        $paymentService = new PaymentService();
        foreach ($allQueues as $q) {
            if ($q->prescription && $q->prescription->total_price <= 0) {
                $paymentService->calculateTotal($q->prescription);
            }
        }

        // Mengelompokkan antrean
        $dalamAntrean = $allQueues->where('status', 'DALAM_ANTREAN');
        $sedangDiracik = $allQueues->where('status', 'SEDANG_DIRACIK');
        $siapDiambil = $allQueues->where('status', 'SIAP_DIAMBIL');
        
        $telahDiserahkan = $allQueues->whereIn('status', ['DISERAHKAN', 'DITERIMA_PASIEN'])
                                     ->sortByDesc('taken_time');

        return view('petugas-loket.dashboard', compact(
            'dalamAntrean',
            'sedangDiracik',
            'siapDiambil',
            'telahDiserahkan'
        ));
    }

    /**
     * [BARU] Fungsi untuk memproses pembayaran tunai di loket
     */
    public function bayarTunai(Request $request, $pharmacyQueueId)
    {
        try {
            $queue = PharmacyQueue::findOrFail($pharmacyQueueId);
            $prescription = $queue->prescription;

            if (!$prescription) {
                return back()->with('error', 'Data resep tidak ditemukan.');
            }

            // Update status pembayaran jadi Lunas
            $prescription->update([
                'payment_status' => 'paid',
                'payment_method' => 'cash', // Tunai lewat loket
                'paid_at' => now(),
            ]);

            return back()->with('success', "Pembayaran tunai untuk antrean {$queue->pharmacy_queue_number} berhasil dikonfirmasi.");

        } catch (\Exception $e) {
            Log::error("Gagal proses bayar tunai: " . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran.');
        }
    }

    /**
     * Update status antrean (Start Racik, Finish Racik, Serahkan)
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

                    // --- VALIDASI PEMBAYARAN DI SINI ---
                    // Cek apakah resep sudah lunas?
                    if ($pharmacyQueue->prescription && $pharmacyQueue->prescription->payment_status !== 'paid') {
                        return redirect()->back()->with('error', 'GAGAL: Pasien belum melunasi tagihan obat. Harap lakukan pembayaran terlebih dahulu.');
                    }

                    $updateData['taken_time'] = now();
                    $successMessage = "Obat untuk {$pharmacyQueue->pharmacy_queue_number} telah diserahkan ke pasien.";
                    break;
                
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

    /**
     * [BARU] Cek status pembayaran ke Midtrans manual oleh Petugas
     */
    public function checkPaymentStatus(Request $request, $pharmacyQueueId)
    {
        try {
            $queue = PharmacyQueue::findOrFail($pharmacyQueueId);
            $prescription = $queue->prescription;

            if (!$prescription) {
                return back()->with('error', 'Data resep tidak ditemukan.');
            }

            $paymentService = new PaymentService();
            $isPaid = $paymentService->checkTransactionStatus($prescription);

            if ($isPaid) {
                return back()->with('success', "Status pembayaran diperbarui: LUNAS (Midtrans).");
            } else {
                return back()->with('error', "Status di Midtrans masih BELUM LUNAS.");
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal cek status: ' . $e->getMessage());
        }
    }
}