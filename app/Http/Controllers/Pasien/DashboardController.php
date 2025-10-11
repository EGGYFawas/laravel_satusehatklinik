<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicQueue;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Doctor;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
<<<<<<< Updated upstream
use Illuminate\Support\Facades\Log; // Import Log Facade
=======
use Illuminate\Support\Facades\Log;
>>>>>>> Stashed changes

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pasien.
     */
    public function index()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();
        $today = Carbon::today()->toDateString();
        $antreanBerobat = null;

        if ($patient) {
<<<<<<< Updated upstream
            $antreanBerobat = ClinicQueue::with('poli')
                ->where('patient_id', $patient->id)
                ->whereDate('registration_time', $today)
                ->whereIn('status', ['MENUNGGU', 'DIPANGGIL'])
                ->first();
=======
            // Mengambil antrean berobat aktif milik pasien (termasuk yang sudah selesai pemeriksaan)
            $antreanBerobat = ClinicQueue::with('poli')
                ->where('patient_id', $patient->id)
                ->whereDate('registration_time', $today)
                ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL', 'SELESAI'])
                ->first();
            
            if ($antreanBerobat) {
                // Mengambil antrean yang sedang dipanggil oleh dokter di poli yang sama
                $antreanBerjalan = ClinicQueue::where('poli_id', $antreanBerobat->poli_id)
                    ->whereDate('registration_time', $today)
                    ->where('status', 'DIPANGGIL')
                    ->orderBy('call_time', 'desc')
                    ->first();

                // Mengambil antrean apotek jika pemeriksaan sudah selesai
                if ($antreanBerobat->status === 'SELESAI') {
                    $antreanApotek = PharmacyQueue::where('clinic_queue_id', $antreanBerobat->id)->first();
                }
            }
>>>>>>> Stashed changes
        }

        $polis = Poli::orderBy('name', 'asc')->get();
        $articles = Article::whereNotNull('published_at')
                            ->latest('published_at')
                            ->take(3)
                            ->get();

        return view('pasien.dashboard', compact('user', 'patient', 'antreanBerobat', 'polis', 'articles'));
    }

<<<<<<< Updated upstream
    /**
     * Menyimpan antrean klinik baru, baik untuk diri sendiri maupun keluarga.
     */
=======
>>>>>>> Stashed changes
    public function store(Request $request)
    {
        $user = Auth::user();
        $isFamilyRegistration = filter_var($request->input('is_family'), FILTER_VALIDATE_BOOLEAN);

        $baseRules = [
            'poli_id' => 'required|exists:polis,id',
            'doctor_id' => 'required|exists:doctors,id',
            'chief_complaint' => 'required|string|min:5|max:255',
            'registration_date' => 'required|date',
        ];

        $familyRules = [];
        if ($isFamilyRegistration) {
            $familyRules = [
                'new_patient_name' => 'required|string|max:255',
<<<<<<< Updated upstream
                // Validasi unique di sini tetap penting sebagai lapisan pertama
=======
>>>>>>> Stashed changes
                'new_patient_nik' => 'required|string|digits:16|unique:patients,nik',
                'new_patient_dob' => 'required|date|before_or_equal:today',
                'new_patient_gender' => 'required|in:Laki-laki,Perempuan',
                'patient_relationship' => 'required|string',
                'patient_relationship_custom' => 'nullable|string|max:100|required_if:patient_relationship,Lainnya',
            ];
        }
        
        $validator = Validator::make($request->all(), array_merge($baseRules, $familyRules));

        if ($validator->fails()) {
<<<<<<< Updated upstream
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Terdapat kesalahan pada data yang Anda masukkan. Silakan periksa kembali.');
=======
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Terdapat kesalahan pada data yang Anda masukkan.');
>>>>>>> Stashed changes
        }

        try {
            DB::beginTransaction();

            $patientForQueue = null;
            $relationship = 'Diri Sendiri';
            $customRelationship = null;

            if ($isFamilyRegistration) {
<<<<<<< Updated upstream
                // --- PERBAIKAN KRUSIAL: Mengganti create() dengan firstOrCreate() ---
                $patientForQueue = Patient::firstOrCreate(
                    ['nik' => $request->new_patient_nik], // Kunci unik untuk mencari
                    [ // Data untuk diisi jika tidak ditemukan
=======
                $patientForQueue = Patient::firstOrCreate(
                    ['nik' => $request->new_patient_nik],
                    [
>>>>>>> Stashed changes
                        'full_name' => $request->new_patient_name,
                        'date_of_birth' => $request->new_patient_dob,
                        'gender' => $request->new_patient_gender,
                        'user_id' => null,
                    ]
                );
                
                $relationship = $request->patient_relationship;
                if ($relationship === 'Lainnya') {
                    $customRelationship = $request->patient_relationship_custom;
                }

            } else {
                $patientForQueue = Patient::where('user_id', $user->id)->first();
                if (!$patientForQueue) {
                    return redirect()->back()->with('error', 'Data profil pasien Anda tidak ditemukan.');
                }
            }

            $registrationDate = Carbon::parse($request->registration_date)->toDateString();
            $existingAntrean = ClinicQueue::where('patient_id', $patientForQueue->id)
                ->whereDate('registration_time', $registrationDate)
<<<<<<< Updated upstream
                ->whereIn('status', ['MENUNGGU', 'DIPANGGIL'])
                ->exists();
            
            if ($existingAntrean) {
                 return redirect()->back()->with('error', 'Pasien yang didaftarkan sudah memiliki antrean aktif untuk hari ini.');
=======
                ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL'])
                ->exists();
            
            if ($existingAntrean) {
                return redirect()->back()->with('error', 'Pasien yang didaftarkan sudah memiliki antrean aktif untuk hari ini.');
>>>>>>> Stashed changes
            }

            $poli = Poli::findOrFail($request->poli_id);
            $lastQueueCount = ClinicQueue::where('poli_id', $request->poli_id)->whereDate('registration_time', $registrationDate)->count();
            $queueNumber = $poli->code . '-' . str_pad($lastQueueCount + 1, 3, '0', STR_PAD_LEFT);

            ClinicQueue::create([
                'patient_id' => $patientForQueue->id,
                'poli_id' => $request->poli_id,
                'doctor_id' => $request->doctor_id,
                'registered_by_user_id' => $user->id,
                'queue_number' => $queueNumber,
                'chief_complaint' => $request->chief_complaint,
                'patient_relationship' => $relationship,
                'patient_relationship_custom' => $customRelationship,
                'status' => 'MENUNGGU',
                'registration_time' => Carbon::parse($registrationDate . ' ' . now()->format('H:i:s')),
            ]);

            DB::commit();

            return redirect()->route('pasien.dashboard')->with('success', 'Pendaftaran antrean berhasil!');

<<<<<<< Updated upstream
        } catch (\Throwable $e) { // Menangkap semua jenis error/exception
=======
        } catch (\Throwable $e) {
>>>>>>> Stashed changes
            DB::rollBack();
            
            // PERBAIKAN: Menambahkan log yang lebih detail
            Log::error('Gagal membuat antrean: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString() // Memberikan trace lengkap error
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server. Gagal membuat antrean.');
        }
    }

    /**
     * API untuk mengambil data dokter berdasarkan poli dan jadwal.
     */
    public function getDoctorsByPoli($poli_id)
    {
        Carbon::setLocale('id');
        $dayName = ucfirst(Carbon::now()->dayName);

        $doctors = Doctor::where('poli_id', $poli_id)
            ->whereHas('doctorSchedules' , function ($query) use ($dayName) {
                $query->where('day_of_week', $dayName)->where('is_active', true);
            })
            ->with('user')
            ->get()
            ->map(function($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->full_name ?? 'Dokter (Nama tidak tersedia)'
                ];
            });

        return response()->json($doctors);
    }
}

