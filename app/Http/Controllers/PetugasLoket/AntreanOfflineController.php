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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AntreanOfflineController extends Controller
{
    /**
     * Menampilkan halaman antrean offline dengan semua data yang relevan.
     */
    public function index()
    {
        $today = Carbon::today();

        // Data untuk Kartu Antrean Berobat
        $pasienMenungguVerifikasi = ClinicQueue::whereDate('registration_time', $today)
            ->where('status', 'MENUNGGU')
            ->with('patient.user')
            ->orderBy('registration_time', 'asc')
            ->get();

        $antreanBerobatBerjalan = ClinicQueue::whereDate('registration_time', $today)
            ->where('status', 'DIPANGGIL')
            ->orderBy('call_time', 'desc')
            ->first();

        $totalAntreanBerobat = ClinicQueue::whereDate('registration_time', $today)->count();

        // Data untuk Kartu Antrean Apotek
        $daftarAntreanApotek = PharmacyQueue::whereDate('pharmacy_queues.created_at', $today)
            // PERBAIKAN: Menambahkan nama tabel 'pharmacy_queues' untuk memperjelas kolom 'status'
            ->whereIn('pharmacy_queues.status', ['DALAM_ANTREAN', 'SEDANG_DIRACIK', 'SIAP_DIAMBIL'])
            ->join('clinic_queues', 'pharmacy_queues.clinic_queue_id', '=', 'clinic_queues.id')
            ->join('patients', 'clinic_queues.patient_id', '=', 'patients.id')
            ->join('users', 'patients.user_id', '=', 'users.id')
            ->select('pharmacy_queues.*', 'users.full_name as patient_name')
            ->orderBy('pharmacy_queues.created_at', 'asc')
            ->get();
            
        $antreanApotekBerjalan = PharmacyQueue::whereDate('created_at', $today)
            ->whereIn('status', ['SEDANG_DIRACIK', 'SIAP_DIAMBIL'])
            ->orderBy('updated_at', 'asc')
            ->first();

        $totalAntreanApotek = PharmacyQueue::whereDate('created_at', $today)->count();

        // Data untuk Form
        $polis = Poli::orderBy('name', 'asc')->get();

        return view('petugas-loket.antrean_offline', compact(
            'polis',
            'pasienMenungguVerifikasi',
            'antreanBerobatBerjalan',
            'totalAntreanBerobat',
            'daftarAntreanApotek',
            'antreanApotekBerjalan',
            'totalAntreanApotek'
        ));
    }

    /**
     * Menyimpan data pendaftaran pasien offline baru.
     */
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
        
        // Cek NIK unik secara manual untuk memberikan pesan error yang lebih spesifik
        $existingPatientWithNik = Patient::where('nik', $request->new_patient_nik)->first();
        if ($existingPatientWithNik) {
             // Jika NIK sudah ada, tidak perlu validasi unik lagi, kita akan gunakan data pasien ini
        } else {
             // Jika NIK belum ada, pastikan tidak ada user lain yang mendaftarkan NIK ini (jarang terjadi)
             $validator->addRules(['new_patient_nik' => 'unique:patients,nik']);
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Terdapat kesalahan pada data yang Anda masukkan.');
        }

        try {
            DB::beginTransaction();

            // Gunakan `firstOrCreate` untuk mencari pasien berdasarkan NIK, atau buat baru jika tidak ada.
            $patient = Patient::where('nik', $request->new_patient_nik)->first();
            
            if (!$patient) {
                // Buat user baru terlebih dahulu
                $user = User::create([
                    'full_name' => strtoupper($request->new_patient_name),
                    'email' => $request->new_patient_nik . '@klinik.local', // Email dummy
                    'password' => Hash::make($request->new_patient_nik),
                    'role' => 'pasien'
                ]);

                // Buat data pasien yang berelasi dengan user baru
                $patient = Patient::create([
                    'user_id' => $user->id,
                    'nik' => $request->new_patient_nik,
                    'full_name' => strtoupper($request->new_patient_name),
                    'date_of_birth' => $request->new_patient_dob,
                    'gender' => $request->new_patient_gender,
                ]);
            }

            $registrationDate = Carbon::today();

            // Cek apakah pasien ini sudah punya antrean aktif hari ini
            $existingAntrean = ClinicQueue::where('patient_id', $patient->id)
                ->whereDate('registration_time', $registrationDate)
                ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL'])
                ->exists();
            
            if ($existingAntrean) {
                return redirect()->back()->with('error', 'Pasien yang didaftarkan sudah memiliki antrean aktif untuk hari ini.');
            }

            // Generate nomor antrean baru
            $poli = Poli::findOrFail($request->poli_id);
            $lastQueueCount = ClinicQueue::where('poli_id', $request->poli_id)->whereDate('registration_time', $registrationDate)->count();
            $queueNumber = $poli->code . '-' . str_pad($lastQueueCount + 1, 3, '0', STR_PAD_LEFT);

            // Buat antrean baru
            ClinicQueue::create([
                'patient_id' => $patient->id,
                'poli_id' => $request->poli_id,
                'doctor_id' => $request->doctor_id,
                'registered_by_user_id' => Auth::id(), // ID Petugas yang login
                'queue_number' => $queueNumber,
                'chief_complaint' => $request->chief_complaint,
                'status' => 'MENUNGGU',
                'registration_type' => 'OFFLINE', // Menandakan pendaftaran dari loket
                'registration_time' => now(),
            ]);

            DB::commit();
            return redirect()->route('petugas-loket.antrean-offline')->with('success', 'Pasien ' . $patient->full_name . ' berhasil didaftarkan ke antrean!');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal membuat antrean offline: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server. Gagal membuat antrean.');
        }
    }

    /**
     * Menangani proses check-in pasien oleh petugas loket.
     */
    public function checkIn(ClinicQueue $clinicQueue)
    {
        try {
            if ($clinicQueue->status == 'MENUNGGU') {
                $clinicQueue->update([
                    'status' => 'HADIR',
                    'check_in_time' => now()
                ]);
                return redirect()->route('petugas-loket.antrean-offline')->with('success', 'Pasien ' . $clinicQueue->patient->user->full_name . ' berhasil di-check-in.');
            }
            return redirect()->route('petugas-loket.antrean-offline')->with('error', 'Status pasien tidak valid untuk check-in.');
        } catch (\Exception $e) {
            Log::error('Gagal Check-in: ' . $e->getMessage());
            return redirect()->route('petugas-loket.antrean-offline')->with('error', 'Terjadi kesalahan saat proses check-in.');
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
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->full_name ?? 'Dokter (Nama tidak tersedia)'
                ];
            });
            
        return response()->json($doctors);
    }
}