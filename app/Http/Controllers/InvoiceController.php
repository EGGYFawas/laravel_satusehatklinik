<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function download($id)
    {
        // Cari data resep + detail obat + data pasien
        $prescription = Prescription::with(['details.medicine', 'medicalRecord.patient'])
                        ->findOrFail($id);

        // Validasi: Cuma boleh download kalau sudah lunas
        if ($prescription->payment_status !== 'paid') {
            abort(403, 'Tagihan belum lunas, struk tidak dapat dicetak.');
        }

        // Data yang dikirim ke view PDF
        $data = [
            'prescription' => $prescription,
            'patient' => $prescription->medicalRecord->patient,
            'details' => $prescription->details,
            'date' => now()->format('d F Y H:i'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.invoice', $data);
        
        // Download
        return $pdf->download('Struk-Pembayaran-'.$prescription->midtrans_booking_code.'.pdf');
    }
}