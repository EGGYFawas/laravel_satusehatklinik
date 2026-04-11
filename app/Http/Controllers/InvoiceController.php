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
        $prescription = Prescription::with([
            'details.medicine', 
            'medicalRecord.patient',
            'medicalRecord.actions', 
            'pharmacyQueue.pharmacist',
            'medicalRecord.clinicQueue' // PENTING: Untuk cek jalur BPJS
        ])->findOrFail($id);

        if ($prescription->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Tagihan belum lunas, struk tidak dapat dicetak.');
        }

        // --- LOGIKA PENENTUAN PETUGAS ---
        $namaPetugas = 'Admin Farmasi';
        
        $isBPJS = ($prescription->medicalRecord->clinicQueue->payment_method ?? '') === 'BPJS';

        if ($isBPJS) {
            $namaPetugas = 'Klaim BPJS Kesehatan';
        } elseif ($prescription->pharmacyQueue && $prescription->pharmacyQueue->pharmacist) {
            $namaPetugas = $prescription->pharmacyQueue->pharmacist->full_name ?? $prescription->pharmacyQueue->pharmacist->name;
        } elseif ($prescription->payment_method == 'midtrans') {
            $namaPetugas = 'Sistem Otomatis (Midtrans)';
        }

        // --- LOGIKA WAKTU PENGAMBILAN ---
        $waktuAmbil = '-';
        $pharmacyQueue = $prescription->medicalRecord->clinicQueue->pharmacyQueue ?? null;

        if ($pharmacyQueue && $pharmacyQueue->taken_time) {
            $waktuAmbil = Carbon::parse($pharmacyQueue->taken_time)
                ->setTimezone('Asia/Jakarta')
                ->format('d/m/Y H:i') . ' WIB';
        } else {
            $statusApotek = $pharmacyQueue->status ?? 'Proses';
            if ($statusApotek == 'DITERIMA_PASIEN') {
                 $waktuAmbil = 'Sudah Diambil'; 
            } else {
                 $waktuAmbil = 'Menunggu Pengambilan';
            }
        }

        $data = [
            'prescription' => $prescription,
            'patient' => $prescription->medicalRecord->patient,
            'details' => $prescription->details,
            'actions' => $prescription->medicalRecord->actions, 
            'is_bpjs' => $isBPJS, // [BARU] Oper status BPJS ke view PDF
            'petugas' => $namaPetugas, 
            'taken_time' => $waktuAmbil,
            'date_print' => now()->format('d/m/Y H:i'), 
            'klinik_name' => 'Klinik Sehat Selalu', 
            'klinik_address' => 'Jl. Kesehatan No. 123, Jakarta',
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('INV-'.$prescription->id.'.pdf');
    }
}