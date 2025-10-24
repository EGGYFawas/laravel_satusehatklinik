<?php

namespace App\Http\Controllers\PetugasLoket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicQueue;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Doctor;
use App\Models\User;
use App\Models\PharmacyQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AntreanOfflineController extends Controller
{
    // ... method index() dan store() tidak ada perubahan dan sudah benar ...
    public function index()
    {
        $today = Carbon::today();
        $daftarAntreanBerobat = ClinicQueue::whereDate('registration_time', $today)
            ->whereIn('status', ['MENUNGGU', 'HADIR'])
            ->with(['patient.user', 'poli'])
            ->orderBy('registration_time', 'asc')
            ->get();
        $antreanBerobatBerjalan = ClinicQueue::whereDate('registration_time', $today)
            ->where('status', 'DIPANGGIL')
            ->orderBy('call_time', 'desc')
            ->first();
        $totalAntreanBerobat = ClinicQueue::whereDate('registration_time', $today)->count();
        $daftarAntreanApotek = PharmacyQueue::whereDate('pharmacy_queues.created_at', $today)
            ->whereIn('pharmacy_queues.status', ['DALAM_ANTREAN', 'SEDANG_DIRACIK', 'SIAP_DIAMBIL', 'DISERAHKAN'])
            ->join('clinic_queues', 'pharmacy_queues.clinic_queue_id', '=', 'clinic_queues.id')
            ->join('patients', 'clinic_queues.patient_id', '=', 'patients.id')
            ->leftJoin('users', 'patients.user_id', '=', 'users.id')
            ->select('pharmacy_queues.*', DB::raw('COALESCE(users.full_name, patients.full_name) as patient_name'))
            ->orderBy('pharmacy_queues.created_at', 'asc')
            ->get();
        $antreanApotekBerjalan = PharmacyQueue::whereDate('created_at', $today)
            ->whereIn('status', ['SEDANG_DIRACIK', 'SIAP_DIAMBIL', 'DISERAHKAN'])
            ->orderBy('updated_at', 'asc')
            ->first();
        $polis = Poli::orderBy('name', 'asc')->get();
        return view('petugas-loket.antrean_offline', compact('polis', 'daftarAntreanBerobat', 'antreanBerobatBerjalan', 'totalAntreanBerobat', 'daftarAntreanApotek', 'antreanApotekBerjalan'));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_patient_name' => 'required|string|max:255',
            'new_patient_nik' => 'required|string|digits:16',
            'new_patient_dob' => 'required|date|before_or_equal:today',
            'new_patient_gender' => 'required|in:Laki-laki,Perempuan',
            'poli_id' => 'required|exists:polis,id',
            'doctor_id' => 'required|exists:doctors,id',
            'chief_complaint' => 'required|string|min:5|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Terdapat kesalahan pada data yang Anda masukkan.');
        }

        try {
            DB::beginTransaction();

            $patient = Patient::updateOrCreate(
                ['nik' => $request->new_patient_nik],
                [
                    'full_name'     => strtoupper($request->new_patient_name),
                    'date_of_birth' => $request->new_patient_dob,
                    'gender'        => $request->new_patient_gender,
                ]
            );

            $registrationDate = Carbon::today();

            if (ClinicQueue::where('patient_id', $patient->id)->whereDate('registration_time', $registrationDate)->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL'])->exists()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Pasien sudah memiliki antrean aktif untuk hari ini.');
            }

            $poli = Poli::findOrFail($request->poli_id);
            $lastQueueCount = ClinicQueue::where('poli_id', $request->poli_id)->whereDate('registration_time', $registrationDate)->count();
            $queueNumber = $poli->code . '-' . str_pad($lastQueueCount + 1, 3, '0', STR_PAD_LEFT);

            ClinicQueue::create([
                'patient_id' => $patient->id,
                'poli_id' => $request->poli_id,
                'doctor_id' => $request->doctor_id,
                'registered_by_user_id' => Auth::id(),
                'queue_number' => $queueNumber,
                'chief_complaint' => $request->chief_complaint,
                'patient_relationship' => 'Diri Sendiri',
                'status' => 'MENUNGGU',
                'registration_time' => now(),
            ]);

            DB::commit();
            return redirect()->route('petugas-loket.antrean-offline.index')->with('success', 'Pasien ' . $patient->full_name . ' berhasil didaftarkan ke antrean!');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal membuat antrean offline: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            if (config('app.debug')) {
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan pada server. Gagal membuat antrean.');
        }
    }

    /**
     * Menangani proses check-in pasien oleh petugas loket.
     */
    public function checkIn($antreanId) // [PERBAIKAN 1] Mengubah parameter menjadi ID
    {
        try {
            // [PERBAIKAN 2] Mencari antrean secara manual menggunakan ID
            $antrean = ClinicQueue::find($antreanId);

            if (!$antrean) {
                Log::error('Gagal Check-in: Record antrean dengan ID ' . $antreanId . ' tidak ditemukan.');
                return redirect()->route('petugas-loket.antrean-offline.index')->with('error', 'Data antrean tidak ditemukan. Mungkin sudah dihapus.');
            }

            $currentStatus = $antrean->status;
            
            if (trim(strtoupper($currentStatus)) == 'MENUNGGU') {
                $antrean->update([
                    'status' => 'HADIR',
                    'check_in_time' => now()
                ]);

                return redirect()->route('petugas-loket.antrean-offline.index')->with('success', 'Pasien ' . ($antrean->patient->user->full_name ?? $antrean->patient->full_name) . ' berhasil di-check-in.');
            }
            
            Log::warning('Gagal Check-in untuk ID ' . $antrean->id . '. Status tidak valid.', ['status_ditemukan' => $currentStatus]);
            return redirect()->route('petugas-loket.antrean-offline.index')->with('error', 'Status pasien tidak valid untuk check-in. Status saat ini adalah: "' . $currentStatus . '"');

        } catch (\Exception $e) {
            Log::error('Exception saat Check-in untuk ID ' . $antreanId . ': ' . $e->getMessage());
            return redirect()->route('petugas-loket.antrean-offline.index')->with('error', 'Terjadi kesalahan sistem saat proses check-in.');
        }
    }

    /**
     * Mengambil daftar dokter berdasarkan poli yang dipilih untuk dropdown dinamis.
     */
    public function getDoctorsByPoli(Poli $poli)
    {
        Carbon::setLocale('id');
        $dayName = ucfirst(Carbon::now()->dayName);
        $doctors = Doctor::where('poli_id', $poli->id)
            ->whereHas('doctorSchedules', function ($query) use ($dayName) {
                $query->where('day_of_week', $dayName)->where('is_active', true);
            })
            ->with('user')
            ->get()
            ->map(function ($doctor) {
                return ['id' => $doctor->id, 'name' => $doctor->user->full_name ?? 'Dokter (Nama tidak tersedia)'];
            });
        return response()->json($doctors);
    }
}

