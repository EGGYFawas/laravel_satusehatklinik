<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicQueue;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\PharmacyQueue;
use App\Models\Medicine;
use App\Models\DiagnosisTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();

        $tz = config('app.timezone');
        $startOfDay = Carbon::now($tz)->startOfDay();
        $endOfDay = Carbon::now($tz)->endOfDay();

        $allQueues = ClinicQueue::with('patient')
            ->where('doctor_id', $doctor->id)
            ->whereBetween('registration_time', [$startOfDay, $endOfDay])
            ->orderBy('check_in_time', 'asc')
            ->orderBy('queue_number', 'asc')
            ->get();

        $pasienSedangDipanggil = $allQueues->firstWhere('status', 'DIPANGGIL');
        $antreanHadir = $allQueues->where('status', 'HADIR');
        $antreanMenunggu = $allQueues->where('status', 'MENUNGGU');
        $antreanSelesai = $allQueues->whereIn('status', ['SELESAI', 'BATAL']);

        $patientHistory = null;
        $medicines = null;
        $diagnosisTags = null;

        if ($pasienSedangDipanggil) {
            $patientHistory = MedicalRecord::where('patient_id', $pasienSedangDipanggil->patient_id)
                                        ->orderBy('checkup_date', 'desc')->get();
            $medicines = Medicine::where('stock', '>', 0)->orderBy('name', 'asc')->get();
            $diagnosisTags = DiagnosisTag::orderBy('tag_name', 'asc')->get();
        }

        return view('dokter.dashboard', compact(
            'doctor', 'pasienSedangDipanggil', 'antreanHadir', 'antreanMenunggu',
            'antreanSelesai', 'patientHistory', 'medicines', 'diagnosisTags'
        ));
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

        $tz = config('app.timezone');
        $startOfDay = Carbon::now($tz)->startOfDay();
        $endOfDay = Carbon::now($tz)->endOfDay();

        $isCallingAnother = ClinicQueue::where('doctor_id', $doctor->id)
            ->whereBetween('registration_time', [$startOfDay, $endOfDay])
            ->where('status', 'DIPANGGIL')
            ->exists();

        if ($isCallingAnother) {
            return redirect()->back()->with('error', 'Selesaikan pemeriksaan pasien saat ini terlebih dahulu.');
        }

        // [PERBAIKAN KONKRIT] Menggunakan Query Builder untuk mem-bypass Model Events.
        $antrean->update([
            'status'    => 'DIPANGGIL',
            'call_time' => now(),
        ]);
        return redirect()->route('dokter.dashboard')->with('success', "Pasien dengan nomor antrean {$antrean->queue_number} telah dipanggil.");
    }
    
    public function simpanPemeriksaan(Request $request, ClinicQueue $antrean)
    {
        $request->validate([ /* ... aturan validasi Anda ... */ ]);
        
        DB::beginTransaction();
        try {
            // Logika penyimpanan rekam medis dan resep
            $patient = Patient::findOrFail($request->patient_id);
            if ($request->filled('blood_type') || $request->filled('known_allergies') || $request->filled('chronic_diseases')) {
                $patient->update([ 'blood_type' => $patient->blood_type ?? $request->blood_type, 'known_allergies' => $patient->known_allergies ?? $request->known_allergies, 'chronic_diseases' => $patient->chronic_diseases ?? $request->chronic_diseases, ]);
            }
            $medicalRecord = MedicalRecord::create([ 'clinic_queue_id' => $antrean->id, 'patient_id' => $patient->id, 'doctor_id' => $antrean->doctor_id, 'checkup_date' => now(), 'doctor_notes' => $request->doctor_notes, ]);
            if ($request->has('diagnosis_tags')) {
                $tagIds = [];
                foreach ($request->diagnosis_tags as $tagName) {
                    $tag = DiagnosisTag::firstOrCreate(['tag_name' => trim($tagName)]);
                    $tagIds[] = $tag->id;
                }
                $medicalRecord->diagnosisTags()->sync($tagIds);
            }
            if ($request->has('medicines') && count($request->medicines) > 0) {
                $prescription = Prescription::create([ 'medical_record_id' => $medicalRecord->id, 'prescription_date' => now(), ]);
                foreach ($request->medicines as $med) {
                    PrescriptionDetail::create([ 'prescription_id' => $prescription->id, 'medicine_id' => $med['id'], 'quantity' => $med['quantity'], 'dosage' => $med['dosage'], ]);
                    $medicine = Medicine::find($med['id']);
                    $medicine->decrement('stock', $med['quantity']);
                }
                
                $tz = config('app.timezone');
                $startOfDay = Carbon::now($tz)->startOfDay();
                $endOfDay = Carbon::now($tz)->endOfDay();
                $lastPharmacyQueueCount = PharmacyQueue::whereBetween('entry_time', [$startOfDay, $endOfDay])->count();
                $pharmacyQueueNumber = 'APT-' . str_pad($lastPharmacyQueueCount + 1, 3, '0', STR_PAD_LEFT);
                
                PharmacyQueue::create([ 
                    'clinic_queue_id' => $antrean->id, 
                    'prescription_id' => $prescription->id, 
                    'pharmacy_queue_number' => $pharmacyQueueNumber, 
                    'status' => 'DALAM_ANTREAN',
                    'entry_time' => now(), 
                ]);
            }
            
            // [PERBAIKAN KONKRIT] Menggunakan Query Builder untuk mem-bypass Model Events.
            $antrean->update([
                'finish_time' => now(),
                'status'      => 'SELESAI',
            ]);
            DB::commit();
            return redirect()->route('dokter.dashboard')->with('success', 'Pemeriksaan selesai dan rekam medis berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan pemeriksaan: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Terjadi kesalahan. Gagal menyimpan data pemeriksaan.');
        }
    }
}

