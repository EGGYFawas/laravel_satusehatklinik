<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicQueue;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Doctor;
use App\Models\Article;
use App\Models\PharmacyQueue;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();
        $today = Carbon::today()->toDateString();
        $antreanBerobat = null;
        $antreanBerjalan = null;
        $antreanApotek = null;

        if ($patient) {
            // -- LOGIKA PENGAMBILAN DATA YANG DIPERBARUI --
            // 1. Ambil antrean berobat aktif (termasuk yang sudah selesai untuk dicek resepnya)
            $latestClinicQueue = ClinicQueue::with('poli')
                ->where('patient_id', $patient->id)
                ->whereDate('registration_time', $today)
                ->orderBy('registration_time', 'desc')
                ->first();

            if ($latestClinicQueue) {
                // Tampilkan antrean berobat jika statusnya belum 'SELESAI' atau 'BATAL'
                if (!in_array($latestClinicQueue->status, ['SELESAI', 'BATAL'])) {
                    $antreanBerobat = $latestClinicQueue;
                }

                // 2. Ambil antrean yang sedang berjalan di poli yang sama
                $antreanBerjalan = ClinicQueue::where('poli_id', $latestClinicQueue->poli_id)
                    ->whereDate('registration_time', $today)
                    ->where('status', 'DIPANGGIL')
                    ->orderBy('call_time', 'desc')
                    ->first();

                // 3. Ambil antrean apotek jika antrean berobat sudah selesai dan ada resep
                if (in_array($latestClinicQueue->status, ['SELESAI', 'MENUNGGU_RACIK', 'SEDANG_DIRACIK', 'SIAP_DIAMBIL'])) {
                    $antreanApotek = PharmacyQueue::where('clinic_queue_id', $latestClinicQueue->id)
                        ->whereIn('status', ['MENUNGGU_RACIK', 'SEDANG_DIRACIK', 'SIAP_DIAMBIL'])
                        ->first();
                }
            }
        }

        $polis = Poli::orderBy('name', 'asc')->get();
        $articles = Article::whereNotNull('published_at')
            ->latest('published_at')->take(3)->get();

        return view('pasien.dashboard', compact('user', 'patient', 'antreanBerobat', 'antreanBerjalan', 'antreanApotek', 'polis', 'articles'));
    }
    
    // ... (Sisa method store() dan getDoctorsByPoli() tidak perlu diubah)
    public function store(Request $request)
    {
        $user = Auth::user();
        $isFamilyRegistration = filter_var($request->input('is_family'), FILTER_VALIDATE_BOOLEAN);

        $baseRules = [ 'poli_id' => 'required|exists:polis,id', 'doctor_id' => 'required|exists:doctors,id', 'chief_complaint' => 'required|string|min:5|max:255', 'registration_date' => 'required|date', ];
        $familyRules = [];
        if ($isFamilyRegistration) {
            $familyRules = [ 'new_patient_name' => 'required|string|max:255', 'new_patient_nik' => 'required|string|digits:16|unique:patients,nik', 'new_patient_dob' => 'required|date|before_or_equal:today', 'new_patient_gender' => 'required|in:Laki-laki,Perempuan', 'patient_relationship' => 'required|string', 'patient_relationship_custom' => 'nullable|string|max:100|required_if:patient_relationship,Lainnya', ];
        }
        
        $validator = Validator::make($request->all(), array_merge($baseRules, $familyRules));
        if ($validator->fails()) { return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Terdapat kesalahan pada data yang Anda masukkan.'); }

        try {
            DB::beginTransaction();
            $patientForQueue = null;
            $relationship = 'Diri Sendiri';
            $customRelationship = null;

            if ($isFamilyRegistration) {
                $patientForQueue = Patient::firstOrCreate( ['nik' => $request->new_patient_nik], [ 'full_name' => $request->new_patient_name, 'date_of_birth' => $request->new_patient_dob, 'gender' => $request->new_patient_gender, 'user_id' => null, ] );
                $relationship = $request->patient_relationship;
                if ($relationship === 'Lainnya') { $customRelationship = $request->patient_relationship_custom; }
            } else {
                $patientForQueue = Patient::where('user_id', $user->id)->first();
                if (!$patientForQueue) { return redirect()->back()->with('error', 'Data profil pasien Anda tidak ditemukan.'); }
            }

            $registrationDate = Carbon::parse($request->registration_date)->toDateString();
            $existingAntrean = ClinicQueue::where('patient_id', $patientForQueue->id) ->whereDate('registration_time', $registrationDate) ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL']) ->exists();
            
            if ($existingAntrean) { return redirect()->back()->with('error', 'Pasien yang didaftarkan sudah memiliki antrean aktif untuk hari ini.'); }

            $poli = Poli::findOrFail($request->poli_id);
            $lastQueueCount = ClinicQueue::where('poli_id', $request->poli_id)->whereDate('registration_time', $registrationDate)->count();
            $queueNumber = $poli->code . '-' . str_pad($lastQueueCount + 1, 3, '0', STR_PAD_LEFT);

            ClinicQueue::create([ 'patient_id' => $patientForQueue->id, 'poli_id' => $request->poli_id, 'doctor_id' => $request->doctor_id, 'registered_by_user_id' => $user->id, 'queue_number' => $queueNumber, 'chief_complaint' => $request->chief_complaint, 'patient_relationship' => $relationship, 'patient_relationship_custom' => $customRelationship, 'status' => 'MENUNGGU', 'registration_time' => Carbon::parse($registrationDate . ' ' . now()->format('H:i:s')), ]);
            DB::commit();
            return redirect()->route('pasien.dashboard')->with('success', 'Pendaftaran antrean berhasil!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal membuat antrean: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server. Gagal membuat antrean.');
        }
    }

    public function getDoctorsByPoli($poli_id)
    {
        Carbon::setLocale('id');
        $dayName = ucfirst(Carbon::now()->dayName);
        $doctors = Doctor::where('poli_id', $poli_id) ->whereHas('doctorSchedules' , function ($query) use ($dayName) { $query->where('day_of_week', $dayName)->where('is_active', true); }) ->with('user') ->get() ->map(function($doctor) { return [ 'id' => $doctor->id, 'name' => $doctor->user->full_name ?? 'Dokter (Nama tidak tersedia)' ]; });
        return response()->json($doctors);
    }
}

