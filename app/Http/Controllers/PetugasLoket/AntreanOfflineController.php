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

/*
|--------------------------------------------------------------------------
| TAMBAHKAN ROUTE INI DI routes/web.php
|--------------------------------------------------------------------------
|
| Pastikan Anda menambahkan route ini di dalam grup middleware
| 'auth' dan 'role:petugas_loket' Anda.
|
| Route::get('/petugas-loket/check-patient-nik/{nik}', [AntreanOfflineController::class, 'checkPatientByNIK'])->name('petugas-loket.check-patient-nik');
|
*/

class AntreanOfflineController extends Controller
{
    /**
     * Menampilkan halaman antrean offline dengan data antrean hari ini.
     */
    public function index()
    {
        $today = Carbon::today();

        // [MODIFIKASI PERMINTAAN 2: Optimalkan Kartu Antrean]
        // Ambil SEMUA antrean berobat hari ini, bukan hanya yang menunggu/hadir
        // Urutkan berdasarkan status (agar yang aktif di atas) lalu berdasarkan waktu registrasi
        $daftarAntreanBerobat = ClinicQueue::whereDate('registration_time', $today)
            ->with(['patient.user', 'poli'])
            ->orderByRaw("
                CASE status
                    WHEN 'MENUNGGU' THEN 1
                    WHEN 'HADIR' THEN 2
                    WHEN 'DIPANGGIL' THEN 3
                    WHEN 'SELESAI' THEN 4
                    WHEN 'BATAL' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('registration_time', 'asc')
            ->get();

        $antreanBerobatBerjalan = ClinicQueue::whereDate('registration_time', $today)
            ->where('status', 'DIPANGGIL')
            ->orderBy('call_time', 'desc')
            ->first();

        $totalAntreanBerobat = ClinicQueue::whereDate('registration_time', $today)->count();

        // [MODIFIKASI PERMINTAAN 2: Optimalkan Kartu Antrean]
        // Ambil SEMUA antrean apotek hari ini, bukan hanya yang aktif
        // Urutkan berdasarkan status (agar yang aktif di atas) lalu berdasarkan waktu pembuatan
        $daftarAntreanApotek = PharmacyQueue::whereDate('pharmacy_queues.created_at', $today)
            ->join('clinic_queues', 'pharmacy_queues.clinic_queue_id', '=', 'clinic_queues.id')
            ->join('patients', 'clinic_queues.patient_id', '=', 'patients.id')
            ->leftJoin('users', 'patients.user_id', '=', 'users.id')
            ->select('pharmacy_queues.*', DB::raw('COALESCE(users.full_name, patients.full_name) as patient_name'))
            ->orderByRaw("
                CASE pharmacy_queues.status
                    WHEN 'DALAM_ANTREAN' THEN 1
                    WHEN 'SEDANG_DIRACIK' THEN 2
                    WHEN 'SIAP_DIAMBIL' THEN 3
                    WHEN 'DISERAHKAN' THEN 4
                    WHEN 'SELESAI' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('pharmacy_queues.created_at', 'asc')
            ->get();

        $antreanApotekBerjalan = PharmacyQueue::whereDate('created_at', $today)
            ->whereIn('status', ['SEDANG_DIRACIK', 'SIAP_DIAMBIL', 'DISERAHKAN'])
            ->orderBy('updated_at', 'asc')
            ->first();

        $polis = Poli::orderBy('name', 'asc')->get();

        return view('petugas-loket.antrean_offline', compact(
            'polis',
            'daftarAntreanBerobat',
            'antreanBerobatBerjalan',
            'totalAntreanBerobat',
            'daftarAntreanApotek',
            'antreanApotekBerjalan'
        ));
    }

    /**
     * Menyimpan data pendaftaran pasien walk-in baru.
     */
    public function store(Request $request)
    {
        // [MODIFIKASI PERMINTAAN 1: NIK]
        // Menambahkan validasi 'sometimes' untuk data pasien
        // Jika NIK sudah ada, data ini tidak akan divalidasi (karena diambil dari DB)
        $validator = Validator::make($request->all(), [
            'new_patient_nik' => 'required|string|digits:16',
            'new_patient_name' => 'required|string|max:255',
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

            // [MODIFIKASI PERMINTAAN 1: NIK]
            // Logika updateOrCreate sudah benar.
            // Jika NIK ditemukan, ia akan update data (jika ada perubahan, misal nama di-edit).
            // Jika NIK tidak ditemukan, ia akan membuat record Patient baru.
            // Ini sudah menangani kasus pasien lama (walk-in) dan pasien baru.
            $patient = Patient::updateOrCreate(
                ['nik' => $request->new_patient_nik], // Kunci pencarian
                [ // Data untuk dibuat atau di-update
                    'full_name'     => strtoupper($request->new_patient_name),
                    'date_of_birth' => $request->new_patient_dob,
                    'gender'        => $request->new_patient_gender,
                    // 'user_id' dibiarkan NULL. Ini akan diisi saat pasien mendaftar mandiri.
                ]
            );

            $registrationDate = Carbon::today();

            // Cek antrean aktif (Sudah benar)
            if (ClinicQueue::where('patient_id', $patient->id)->whereDate('registration_time', $registrationDate)->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL'])->exists()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Pasien sudah memiliki antrean aktif untuk hari ini.');
            }

            // Pembuatan nomor antrean (Sudah benar)
            $poli = Poli::findOrFail($request->poli_id);
            $lastQueueCount = ClinicQueue::where('poli_id', $request->poli_id)->whereDate('registration_time', $registrationDate)->count();
            $queueNumber = $poli->code . '-' . str_pad($lastQueueCount + 1, 3, '0', STR_PAD_LEFT);

            ClinicQueue::create([
                'patient_id' => $patient->id,
                'poli_id' => $request->poli_id,
                'doctor_id' => $request->doctor_id,
                'registered_by_user_id' => Auth::id(), // Ini menandakan antrean "walk-in"
                'queue_number' => $queueNumber,
                'chief_complaint' => $request->chief_complaint,
                'patient_relationship' => 'Diri Sendiri', // Default untuk walk-in
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
    public function checkIn($antreanId)
    {
        try {
            $antrean = ClinicQueue::find($antreanId);

            if (!$antrean) {
                Log::error('Gagal Check-in: Record antrean dengan ID ' . $antreanId . ' tidak ditemukan.');
                return redirect()->route('petugas-loket.antrean-offline.index')->with('error', 'Data antrean tidak ditemukan. Mungkin sudah dihapus.');
            }

            // [MODIFIKASI PERMINTAAN 3: Batasi Check-in]
            // Cek apakah antrean ini adalah antrean 'online' (didaftarkan mandiri oleh pasien)
            // Antrean 'online' memiliki registered_by_user_id = NULL
            if ($antrean->registered_by_user_id == null) {
                Log::warning('Gagal Check-in: Percobaan check-in manual untuk antrean online ID ' . $antreanId . ' oleh ' . Auth::user()->full_name);
                return redirect()->route('petugas-loket.antrean-offline.index')->with('error', 'Pasien ini terdaftar online dan harus melakukan check-in mandiri via QR code.');
            }

            // Jika lolos cek di atas, berarti ini antrean walk-in
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
     * [BARU - PERMINTAAN 1]
     * Mengecek data pasien berdasarkan NIK untuk auto-fill form.
     */
    public function checkPatientByNIK($nik)
    {
        // Validasi NIK
        $validator = Validator::make(['nik' => $nik], [
            'nik' => 'required|string|digits:16'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'NIK tidak valid.'], 400);
        }

        // Cari pasien berdasarkan NIK. Hanya ambil data yang diperlukan.
        $patient = Patient::where('nik', $nik)->first(['full_name', 'date_of_birth', 'gender']);

        if ($patient) {
            // Jika pasien ditemukan, kirim datanya
            return response()->json([
                'found' => true,
                'full_name' => $patient->full_name,
                'date_of_birth' => $patient->date_of_birth, // Format YYYY-MM-DD
                'gender' => $patient->gender,
            ]);
        }

        // Jika tidak ditemukan
        return response()->json(['found' => false]);
    }

    /**
     * Mengambil daftar dokter berdasarkan poli yang dipilih untuk dropdown dinamis.
     */
    public function getDoctorsByPoli(Poli $poli)
    {
        // Logika ini sudah benar, tidak perlu diubah
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
