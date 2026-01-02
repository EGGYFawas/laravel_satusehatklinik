<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\Patient;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // [PENTING] Import Carbon untuk tanggal

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cari data pasien yang login
        $patient = Patient::where('user_id', $user->id)->first();
        
        // Jika tidak ada data pasien (misal testing admin), default ke ID 1 atau handle error
        $patientId = $patient ? $patient->id : 1;

        // Ambil resep/tagihan milik pasien
        $bills = Prescription::whereHas('medicalRecord', function ($query) use ($patientId) {
                    $query->where('patient_id', $patientId);
                })
                // [SOLUSI UTAMA] Filter hanya ambil data HARI INI
                ->whereDate('created_at', Carbon::today())
                ->with(['medicalRecord.doctor', 'details.medicine'])
                ->latest()
                ->get();

        return view('pasien.billing.index', compact('bills'));
    }

    public function pay(Prescription $prescription)
    {
        try {
            $service = new PaymentService();
            $snapToken = $service->getSnapToken($prescription);

            if ($snapToken) {
                // Redirect user Android ke halaman pembayaran Midtrans
                return redirect()->away("https://app.sandbox.midtrans.com/snap/v2/vtweb/" . $snapToken);
            }

            return back()->with('error', 'Gagal memproses token pembayaran.');

        } catch (\Exception $e) {
            return back()->with('error', 'Midtrans Error: ' . $e->getMessage());
        }
    }

    // Fungsi cek status manual (Refresh Status)
    public function checkStatus(Prescription $prescription)
    {
        $service = new PaymentService();
        $isPaid = $service->checkTransactionStatus($prescription);

        if ($isPaid) {
            return back()->with('success', 'Pembayaran berhasil diverifikasi! Tagihan lunas.');
        } else {
            return back()->with('error', 'Status pembayaran masih tertunda atau belum dibayar.');
        }
    }
}