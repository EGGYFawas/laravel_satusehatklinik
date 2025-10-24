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

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk dokter.
     */
    public function index()
    {
        $user = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();

        $tz = config('app.timezone');
        $startOfDay = Carbon::now($tz)->startOfDay();
        $endOfDay = Carbon::now($tz)->endOfDay();

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

        $isCallingAnother = ClinicQueue::where('doctor_id', $doctor->id)
            ->whereDate('registration_time', today(config('app.timezone')))
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
     * Method untuk menyimpan hasil pemeriksaan dengan otorisasi yang sudah diperbaiki.
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
            'diagnosis_tags' => 'required|array|min:1',
            'diagnosis_tags.*' => 'required|string|max:100',
            'doctor_notes' => 'required|string|min:5',
            'medicines' => 'nullable|array',
            'medicines.*.id' => 'required|exists:medicines,id',
            'medicines.*.quantity' => 'required|integer|min:1',
            'medicines.*.dosage' => 'required|string|max:255',
        ], [
            'diagnosis_tags.required' => 'Diagnosis (Asesmen) wajib diisi.',
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
            ]);

            $tagIds = [];
            foreach ($validatedData['diagnosis_tags'] as $tagName) {
                $tag = DiagnosisTag::firstOrCreate(['tag_name' => trim($tagName)]);
                $tagIds[] = $tag->id;
            }
            $medicalRecord->diagnosisTags()->sync($tagIds);

            if (!empty($validatedData['medicines'])) {
                $prescription = Prescription::create([
                    'medical_record_id' => $medicalRecord->id,
                    'prescription_date' => now(),
                ]);

                foreach ($validatedData['medicines'] as $med) {
                    PrescriptionDetail::create([
                        'prescription_id' => $prescription->id,
                        'medicine_id' => $med['id'],
                        'quantity' => $med['quantity'],
                        'dosage' => $med['dosage'],
                    ]);
                    $medicine = Medicine::find($med['id']);
                    if ($medicine->stock < $med['quantity']) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('error', "Stok obat {$medicine->name} tidak mencukupi.");
                    }
                    $medicine->decrement('stock', $med['quantity']);
                }
                
                // [MODIFIKASI UTAMA] Mengembalikan logika pemformatan nomor antrean apotek
                $nextQueueNumberInt = PharmacyQueue::generateQueueNumber(); // Dapatkan angka berikutnya (misal: 1, 2, 3)
                $formattedQueueNumber = 'APT-' . str_pad($nextQueueNumberInt, 3, '0', STR_PAD_LEFT); // Format menjadi APT-001, APT-002, dst.

                PharmacyQueue::create([
                    'clinic_queue_id' => $antrean->id,
                    'prescription_id' => $prescription->id,
                    'pharmacy_queue_number' => $formattedQueueNumber, // Gunakan nomor yang sudah diformat
                    'status' => 'DALAM_ANTREAN',
                    'entry_time' => now(),
                ]);
            }

            $antrean->update([
                'finish_time' => now(),
                'status' => 'SELESAI',
            ]);

            DB::commit();
            return redirect()->route('dokter.dashboard')->with('success', 'Pemeriksaan selesai dan rekam medis berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan pemeriksaan: {$e->getMessage()}");
             // Tampilkan pesan error yang sebenarnya untuk debugging
             return redirect()->back()->withInput()->with('error', 'DEBUG: ' . $e->getMessage());
        }
    }
}

