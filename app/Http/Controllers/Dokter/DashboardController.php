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
use App\Models\Icd10; // Import Model ICD10

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk dokter.
     */
    public function index()
    {
        $user = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();

        // Gunakan helper now() agar konsisten dengan timezone aplikasi (Asia/Jakarta)
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        $allQueues = ClinicQueue::with('patient.user')
            ->where('doctor_id', $doctor->id)
            // Filter antrean HANYA hari ini
            ->whereBetween('registration_time', [$startOfDay, $endOfDay])
            ->orderBy('check_in_time', 'asc') // Urutkan yang sudah check-in duluan
            ->orderBy('queue_number', 'asc')  // Lalu urutkan nomor antrean
            ->get();

        $pasienSedangDipanggil = $allQueues->firstWhere('status', 'DIPANGGIL');
        
        $pasienBerikutnya = null;
        if(!$pasienSedangDipanggil) {
            // Prioritaskan pasien yang statusnya HADIR (sudah ada di klinik)
            $pasienBerikutnya = $allQueues->where('status', 'HADIR')->first();
        }
        
        $antreanHadir = $allQueues->where('status', 'HADIR')->when($pasienBerikutnya, function ($query) use ($pasienBerikutnya) {
            return $query->where('id', '!=', $pasienBerikutnya->id);
        });

        $antreanMenunggu = $allQueues->where('status', 'MENUNGGU');
        $antreanSelesai = $allQueues->whereIn('status', ['SELESAI', 'BATAL'])->sortByDesc('finish_time');

        $medicines = null;
        $diagnosisTags = null;
        
        $pasienAktif = $pasienSedangDipanggil ?? $pasienBerikutnya;

        if ($pasienAktif) {
            $medicines = Medicine::where('stock', '>', 0)->orderBy('name', 'asc')->get(['id', 'name', 'stock']);
            $diagnosisTags = DiagnosisTag::orderBy('tag_name', 'asc')->get(['tag_name']);
        }

        return view('dokter.dashboard', compact(
            'doctor', 'pasienSedangDipanggil', 'pasienBerikutnya', 'antreanHadir', 'antreanMenunggu',
            'antreanSelesai', 'medicines', 'diagnosisTags'
        ));
    }

    /**
     * [BARU] Method AJAX untuk mencari ICD-10 dari database (Server-side Filtering)
     */
    public function searchIcd10(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([]);
        }

        // Cari berdasarkan kode atau nama, batasi 30 hasil agar ringan
        $results = Icd10::where('code', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->limit(30)
            ->get(['code', 'name']);

        return response()->json($results);
    }

    /**
     * Memanggil pasien berikutnya dalam antrean.
     */
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

        // Cek apakah ada pasien lain yang MASIH status 'DIPANGGIL' hari ini
        $isCallingAnother = ClinicQueue::where('doctor_id', $doctor->id)
            ->whereDate('registration_time', today()) // today() mengikuti timezone app
            ->where('status', 'DIPANGGIL')
            ->exists();

        if ($isCallingAnother) {
            return redirect()->back()->with('error', 'Selesaikan pemeriksaan pasien saat ini terlebih dahulu.');
        }

        $antrean->update([
            'status'    => 'DIPANGGIL',
            'call_time' => now(),
        ]);
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
            
            // Diagnosis Utama (Wajib ICD 10)
            'icd10_code' => 'required|string',
            'icd10_name' => 'required|string',

            // Diagnosis Tambahan (Tags - Opsional)
            'diagnosis_tags' => 'nullable|array', 
            'diagnosis_tags.*' => 'string|max:100',
            
            'doctor_notes' => 'required|string|min:5',
            'medicines' => 'nullable|array',
            'medicines.*.id' => 'required|exists:medicines,id',
            'medicines.*.quantity' => 'required|integer|min:1',
            'medicines.*.dosage' => 'required|string|max:255',
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

            // Simpan Rekam Medis
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

            // Simpan Diagnosis Tambahan
            $tagIds = [];
            if (!empty($validatedData['diagnosis_tags'])) {
                foreach ($validatedData['diagnosis_tags'] as $tagName) {
                    $tag = DiagnosisTag::firstOrCreate(['tag_name' => trim($tagName)]);
                    $tagIds[] = $tag->id;
                }
                $medicalRecord->diagnosisTags()->sync($tagIds);
            }

            if (!empty($validatedData['medicines'])) {
                $prescription = Prescription::create([
                    'medical_record_id' => $medicalRecord->id,
                    'prescription_date' => now(),
                ]);

                foreach ($validatedData['medicines'] as $med) {
                    $medicine = Medicine::find($med['id']);
                    
                    // Cek stok sebelum insert detail agar tidak minus
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
                
                // --- [PERBAIKAN BUG KRUSIAL] ---
                // Jangan gunakan Model statis yang berisiko salah casting string ke int.
                // Hitung manual di sini agar antrean apotek selalu unique dan berurut.
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

            // Update Status Antrean Klinik jadi SELESAI
            // Jika ini tidak tereksekusi (karena error di atas), dokter akan stuck.
            $antrean->update([
                'finish_time' => now(),
                'status' => 'SELESAI',
            ]);

            DB::commit();
            return redirect()->route('dokter.dashboard')->with('success', 'Pemeriksaan selesai dan rekam medis berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan pemeriksaan: {$e->getMessage()}");
             return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}