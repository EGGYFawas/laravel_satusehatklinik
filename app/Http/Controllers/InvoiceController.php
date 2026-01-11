<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function download($id)
    {
        // Cari data resep + detail obat + data pasien
        // [PENTING] Kita tambahkan eager loading ke 'pharmacyQueue.pharmacist' 
        // Asumsi: 
        // 1. Di model Prescription ada function pharmacyQueue() { return $this->hasOne(PharmacyQueue::class); }
        // 2. Di model PharmacyQueue ada function pharmacist() { return $this->belongsTo(User::class, 'user_id'); }
        $prescription = Prescription::with([
            'details.medicine', 
            'medicalRecord.patient',
            'medicalRecord.actions', // <-- PENTING: Ambil data tindakan
            'pharmacyQueue.pharmacist' 
        ])->findOrFail($id);

        // Validasi: Cuma boleh download kalau sudah lunas
        if ($prescription->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Tagihan belum lunas, struk tidak dapat dicetak.');
        }

        // --- LOGIKA PENENTUAN PETUGAS (DIPERBAIKI) ---
        
        // 1. Set Default Awal
        $namaPetugas = 'Admin Farmasi';
        
        // 2. Cek apakah ada antrian farmasi DAN ada petugas (user) yang tercatat menangani
        //    Ini berlaku untuk CASH maupun MIDTRANS. Jika obat sudah diracik/diserahkan oleh orang, nama orang itu yang muncul.
        if ($prescription->pharmacyQueue && $prescription->pharmacyQueue->pharmacist) {
            // Prioritaskan full_name, fallback ke name biasa
            $namaPetugas = $prescription->pharmacyQueue->pharmacist->full_name ?? $prescription->pharmacyQueue->pharmacist->name;
        } 
        // 3. Jika TIDAK ada petugas (misal: baru bayar online tapi obat belum diproses/diambil)
        elseif ($prescription->payment_method == 'midtrans') {
            $namaPetugas = 'Sistem Otomatis (Midtrans)';
        }

        // --- LOGIKA WAKTU PENGAMBILAN (TAKEN TIME) ---
        
        $waktuAmbil = '-';

        // [FIX RELASI] Mengakses PharmacyQueue melalui MedicalRecord -> ClinicQueue
        $pharmacyQueue = $prescription->medicalRecord->clinicQueue->pharmacyQueue ?? null;

        if ($pharmacyQueue && $pharmacyQueue->taken_time) {
            // Gunakan setTimezone('Asia/Jakarta') agar sesuai dengan waktu lokal Indonesia
            $waktuAmbil = Carbon::parse($pharmacyQueue->taken_time)
                ->setTimezone('Asia/Jakarta')
                ->format('d/m/Y H:i') . ' WIB';
        } else {
            // Opsional: Cek status apotek jika taken_time masih kosong
            $statusApotek = $pharmacyQueue->status ?? 'Proses';
            if ($statusApotek == 'DITERIMA_PASIEN') {
                 // Fallback darurat jika status 'Diterima' tapi taken_time null (bug database)
                 $waktuAmbil = 'Sudah Diambil'; 
            } else {
                 $waktuAmbil = 'Menunggu Pengambilan';
            }
        }

        // Data yang dikirim ke view PDF
        $data = [
            'prescription' => $prescription,
            'patient' => $prescription->medicalRecord->patient,
            'details' => $prescription->details,
            'actions' => $prescription->medicalRecord->actions, 
            
            // Variabel dinamis sesuai request
            'petugas' => $namaPetugas, 
            'taken_time' => $waktuAmbil,
            
            'date_print' => now()->format('d/m/Y H:i'), // Waktu cetak PDF (hanya untuk log cetak)
            'klinik_name' => 'Klinik Sehat Selalu', 
            'klinik_address' => 'Jl. Kesehatan No. 123, Jakarta',
        ];

        // Generate PDF
        // Pastikan view 'pdf.invoice' menyesuaikan variabel $petugas dan $taken_time
        $pdf = Pdf::loadView('pdf.invoice', $data);
        
        // Set Paper Size ke A5 atau Thermal (opsional), disini default A4 portrait
        $pdf->setPaper('a4', 'portrait');

        // Download
        return $pdf->download('INV-'.$prescription->midtrans_booking_code.'.pdf');
    }
}