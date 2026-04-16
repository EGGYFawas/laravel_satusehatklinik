<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Import semua model yang dibutuhkan
use App\Models\ClinicQueue;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\DiagnosisTag;
use App\Models\PharmacyQueue;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\Medicine;
use App\Models\Icd10;
use App\Models\MedicalAction;
use App\Models\MedicalRecordAction;
use App\Services\SatuSehatService; // [BARU] Import Service SatuSehat

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk dokter.
     */
    public function index()
    {
        $user = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();

        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        $allQueues = ClinicQueue::with('patient.user')
            ->where('doctor_id', $doctor->id)
            ->whereBetween('registration_time', [$startOfDay, $endOfDay])
            ->orderBy('check_in_time', 'asc')
            ->orderBy('queue_number', 'asc')
            ->get();

        $pasienSedangDipanggil = $allQueues->firstWhere('status', 'DIPANGGIL');
        
        $pasienBerikutnya = null;
        if(!$pasienSedangDipanggil) {
            $pasienBerikutnya = $allQueues->where('status', 'HADIR')->first();
        }
        
        $antreanHadir = $allQueues->where('status', 'HADIR')->when($pasienBerikutnya, function ($query) use ($pasienBerikutnya) {
            return $query->where('id', '!=', $pasienBerikutnya->id);
        });

        $antreanMenunggu = $allQueues->where('status', 'MENUNGGU');
        $antreanSelesai = $allQueues->whereIn('status', ['SELESAI', 'BATAL'])->sortByDesc('finish_time');

        $medicines = null;
        $diagnosisTags = null;
        $availableActions = null;
        
        $pasienAktif = $pasienSedangDipanggil ?? $pasienBerikutnya;

        if ($pasienAktif) {
            $medicines = Medicine::where('stock', '>', 0)->orderBy('name', 'asc')->get(['id', 'name', 'stock']);
            $diagnosisTags = DiagnosisTag::orderBy('tag_name', 'asc')->get(['tag_name']);
            $availableActions = MedicalAction::orderBy('name', 'asc')->get(['id', 'name', 'price']);
        }

        return view('dokter.dashboard', compact(
            'doctor', 'pasienSedangDipanggil', 'pasienBerikutnya', 'antreanHadir', 'antreanMenunggu',
            'antreanSelesai', 'medicines', 'diagnosisTags', 'availableActions'
        ));
    }

    public function searchIcd10(Request $request)
    {
        $query = $request->get('q');
        if (!$query) return response()->json([]);

        $results = Icd10::where('code', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->limit(30)
            ->get(['code', 'name']);

        return response()->json($results);
    }

    public function panggilPasien(ClinicQueue $antrean)
    {
        $user = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();
        
        if ($antrean->doctor_id !== $doctor->id) {
            return redirect()->back()->with('error', 'Anda tidak berhak memanggil antrean ini.');
        }
        
        if ($antrean->status !== 'HADIR') {
            return redirect()->back()->with('error', 'Pasien belum melakukan check-in kehadiran.');
        }

        $isCallingAnother = ClinicQueue::where('doctor_id', $doctor->id)
            ->whereDate('registration_time', today())
            ->where('status', 'DIPANGGIL')
            ->exists();

        if ($isCallingAnother) {
            return redirect()->back()->with('error', 'Selesaikan pemeriksaan pasien saat ini terlebih dahulu.');
        }

        // 1. Update status antrean yang dipanggil
        $antrean->update([
            'status'    => 'DIPANGGIL',
            'call_time' => now(),
        ]);

        // ====================================================================
        // 2. [LOGIKA PUSH NOTIF WA] CEK PASIEN URUTAN KE-2 SELANJUTNYA
        // ====================================================================
        try {
            // Ambil daftar antrean yang masih menunggu/hadir di Poli & Dokter yang sama hari ini
            // Tambahkan 'poli' di eager loading agar tidak berat saat memanggil nama poli
            $antreanSelanjutnya = ClinicQueue::with(['patient.user', 'poli'])
                ->where('doctor_id', $doctor->id)
                ->whereDate('registration_time', today())
                ->whereIn('status', ['MENUNGGU', 'HADIR'])
                ->orderBy('registration_time', 'asc') // Urutkan berdasarkan waktu daftar
                ->get();

            // Cek apakah ada pasien di urutan ke-2 (Index 1 array)
            // Selisih 2 orang = Jika sekarang no 4 masuk, no 5 itu index 0, no 6 itu index 1.
            if ($antreanSelanjutnya->count() >= 2) {
                $pasienTargetWA = $antreanSelanjutnya[1]; // Ambil orang kedua di daftar tunggu
                
                // Cari nomor HP (Bisa dari tabel user kalau dia daftar online, atau tabel patient kalau walk-in)
                $noHp = $pasienTargetWA->patient->user->phone ?? $pasienTargetWA->patient->phone ?? null;

                if (!empty($noHp)) {
                    $namaPasien = $pasienTargetWA->patient->user->full_name ?? $pasienTargetWA->patient->full_name;
                    $noAntreanTarget = $pasienTargetWA->queue_number;
                    $namaPoli = $pasienTargetWA->poli->name ?? 'Poli';
                    $namaKlinik = \App\Models\ClinicSetting::first()->name ?? 'Klinik Sehat';

                    // Buat template pesan WhatsApp
                    $pesan = "🔔 *PENGINGAT ANTREAN - {$namaKlinik}*\n\n"
                           . "Halo Bpk/Ibu *$namaPasien*,\n\n"
                           . "Pemberitahuan bahwa antrean Anda dengan nomor *$noAntreanTarget* di *$namaPoli* akan segera dipanggil (kurang 2 antrean lagi).\n\n"
                           . "Mohon untuk segera bersiap dan menuju ke ruang tunggu depan ruangan dokter.\n\n"
                           . "Terima kasih dan semoga lekas sembuh! 🙏";

                    // Kirim pesan menggunakan Fonnte Service
                    $fonnte = new \App\Services\FonnteService();
                    $fonnte->sendMessage($noHp, $pesan);
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengirim WA Panggilan: " . $e->getMessage());
            // Sengaja tidak di-throw error ke halaman dokter agar proses panggil pasien tetap lancar meskipun WA gagal terkirim
        }
        // ====================================================================

        return redirect()->route('dokter.dashboard')->with('success', "Pasien dengan nomor antrean {$antrean->queue_number} telah dipanggil.");
    }
    
    /**
     * Method untuk menyimpan hasil pemeriksaan.
     */
    public function simpanPemeriksaan(Request $request, ClinicQueue $antrean)
    {
        $user = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();

        if ($antrean->doctor_id !== $doctor->id) {
            return redirect()->route('dokter.dashboard')->with('error', 'Anda tidak berwenang menangani pasien ini.');
        }

        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'blood_pressure_systolic' => 'required|integer|min:0',
            'blood_pressure_diastolic' => 'required|integer|min:0',
            'heart_rate' => 'required|integer|min:0',
            'temperature' => 'required|numeric|min:0',
            'respiratory_rate' => 'nullable|integer|min:0',
            'oxygen_saturation' => 'nullable|integer|min:0',
            'physical_examination_notes' => 'nullable|string|max:2000',
            
            'icd10_code' => 'required|string',
            'icd10_name' => 'required|string',

            'diagnosis_tags' => 'nullable|array', 
            'diagnosis_tags.*' => 'string|max:100',
            
            'doctor_notes' => 'required|string|min:5',
            
            'medicines' => 'nullable|array',
            'medicines.*.id' => 'required|exists:medicines,id',
            'medicines.*.quantity' => 'required|integer|min:1',
            'medicines.*.dosage' => 'required|string|max:255',

            'actions' => 'nullable|array',
            'actions.*.id' => 'required|exists:medical_actions,id',
            'actions.*.result' => 'nullable|string|max:255',
        ], [
            'icd10_code.required' => 'Diagnosis Utama (ICD-10) wajib dipilih.',
            'doctor_notes.required' => 'Rencana Penatalaksanaan (Plan) wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $patient = Patient::findOrFail($validatedData['patient_id']);

            if ($request->filled('blood_type') || $request->filled('known_allergies') || $request->filled('chronic_diseases')) {
                $patient->update([
                    'blood_type' => $request->blood_type,
                    'known_allergies' => $request->known_allergies,
                    'chronic_diseases' => $request->chronic_diseases,
                ]);
            }

            // 1. Simpan Rekam Medis
            $medicalRecord = MedicalRecord::create([
                'clinic_queue_id' => $antrean->id,
                'patient_id' => $patient->id,
                'doctor_id' => $antrean->doctor_id,
                'checkup_date' => now(),
                'doctor_notes' => $validatedData['doctor_notes'],
                'blood_pressure' => $validatedData['blood_pressure_systolic'] . '/' . $validatedData['blood_pressure_diastolic'],
                'heart_rate' => $validatedData['heart_rate'],
                'temperature' => $validatedData['temperature'],
                'respiratory_rate' => $validatedData['respiratory_rate'],
                'oxygen_saturation' => $validatedData['oxygen_saturation'],
                'physical_examination_notes' => $validatedData['physical_examination_notes'],
                'primary_icd10_code' => $validatedData['icd10_code'],
                'primary_icd10_name' => $validatedData['icd10_name'],
            ]);

            // 2. Simpan Diagnosis Tambahan
            $tagIds = [];
            if (!empty($validatedData['diagnosis_tags'])) {
                foreach ($validatedData['diagnosis_tags'] as $tagName) {
                    $tag = DiagnosisTag::firstOrCreate(['tag_name' => trim($tagName)]);
                    $tagIds[] = $tag->id;
                }
                $medicalRecord->diagnosisTags()->sync($tagIds);
            }

            // 3. Simpan Tindakan / Pemeriksaan Tambahan
            if (!empty($validatedData['actions'])) {
                foreach ($validatedData['actions'] as $actionItem) {
                    $masterAction = MedicalAction::find($actionItem['id']);
                    
                    if($masterAction) {
                        MedicalRecordAction::create([
                            'medical_record_id' => $medicalRecord->id,
                            'medical_action_id' => $masterAction->id,
                            'action_name'       => $masterAction->name, 
                            'price'             => $masterAction->price, 
                            'result_notes'      => $actionItem['result'] ?? '-',
                            'created_at'        => now(),
                        ]);
                    }
                }
            }

            // 4. Penentuan Apakah Perlu Tagihan/Resep
            $hasMedicines = !empty($validatedData['medicines']);
            $hasActions   = !empty($validatedData['actions']);
            
            if ($hasMedicines || $hasActions) {
                
                $prescription = Prescription::create([
                    'medical_record_id' => $medicalRecord->id,
                    'prescription_date' => now(),
                    'total_price' => 0, 
                    'payment_status' => 'pending' 
                ]);

                if ($hasMedicines) {
                    foreach ($validatedData['medicines'] as $med) {
                        $medicine = Medicine::lockForUpdate()->find($med['id']);
                        
                        if ($medicine->stock < $med['quantity']) {
                            DB::rollBack();
                            return redirect()->back()->withInput()->with('error', "Stok obat {$medicine->name} tidak mencukupi.");
                        }
                        
                        PrescriptionDetail::create([
                            'prescription_id' => $prescription->id,
                            'medicine_id' => $med['id'],
                            'quantity' => $med['quantity'],
                            'dosage' => $med['dosage'],
                        ]);
                        
                        $medicine->decrement('stock', $med['quantity']);
                    }
                }
                
                $todayStart = now()->startOfDay();
                $countToday = PharmacyQueue::where('created_at', '>=', $todayStart)->count();
                $nextQueueNumberInt = $countToday + 1;
                $formattedQueueNumber = 'APT-' . str_pad($nextQueueNumberInt, 3, '0', STR_PAD_LEFT); 

                PharmacyQueue::create([
                    'clinic_queue_id' => $antrean->id,
                    'prescription_id' => $prescription->id,
                    'pharmacy_queue_number' => $formattedQueueNumber, 
                    'status' => 'DALAM_ANTREAN', 
                    'entry_time' => now(),
                ]);
            }

            // 5. Update Status Antrean
            $antrean->update([
                'finish_time' => now(),
                'status' => 'SELESAI',
            ]);

            DB::commit(); // <--- SIMPAN LOKAL SUKSES

            // ====================================================================
            // [BARU] 6. KIRIM REKAM MEDIS KE SATUSEHAT KEMENKES (NON-BLOCKING)
            // ====================================================================
            try {
                // Hanya kirim jika pasien memiliki IHS Number
                if (!empty($patient->ihs_number)) {
                    $satuSehat = new SatuSehatService();
                    
                    // Kita oper object medical record, ini akan di-handle di Service
                    $response = $satuSehat->sendMedicalRecord($medicalRecord);
                    
                    if ($response['success'] && isset($response['encounter_id'])) {
                        // Simpan ID Kunjungan Kemenkes ke DB kita
                        $medicalRecord->update([
                            'satusehat_encounter_id' => $response['encounter_id']
                        ]);
                        Log::info("SatuSehat Success: MR {$medicalRecord->id} sent to Kemenkes.");
                    } else {
                        Log::warning("SatuSehat Failed: " . ($response['message'] ?? 'Unknown Error'));
                    }
                }
            } catch (\Exception $e) {
                // Jika error, biarkan saja (jangan di-rollback). Data lokal klinik tetap aman!
                Log::error("SatuSehat Exception: " . $e->getMessage());
            }
            // ====================================================================

            return redirect()->route('dokter.dashboard')->with('success', 'Pemeriksaan selesai dan rekam medis berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan pemeriksaan: {$e->getMessage()}");
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}