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
    /**
     * Menampilkan halaman dashboard pasien dengan data antrean atau riwayat terakhir.
     */
    public function index()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        // --- PERBAIKAN ZONA WAKTU (STEP 1) ---
        // Karena .env sudah diubah ke 'Asia/Jakarta', kita cukup pakai now()
        // startOfDay() otomatis mendeteksi jam 00:00:00 WIB hari ini
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();
        // --- AKHIR PERBAIKAN ---

        // Inisialisasi semua variabel
        $antreanBerobat = null;
        $riwayatBerobatTerakhir = null;
        $antreanBerjalan = null;
        $antreanApotek = null;
        $antreanApotekBerjalan = null;
        $jumlahAntreanApotekSebelumnya = 0;

        if ($patient) {
            $kunjunganHariIni = ClinicQueue::with(['poli', 'doctor.user'])
                ->where('patient_id', $patient->id)
                ->whereBetween('registration_time', [$startOfDay, $endOfDay])
                ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL']) 
                ->first();

            // Jika tidak ada antrean aktif, baru cari yang sudah selesai hari ini
            if (!$kunjunganHariIni) {
                $kunjunganHariIni = ClinicQueue::with(['poli', 'doctor.user'])
                    ->where('patient_id', $patient->id)
                    ->whereBetween('registration_time', [$startOfDay, $endOfDay])
                    ->where('status', 'SELESAI')
                    ->first();
            }

            if ($kunjunganHariIni) {
                $antreanBerobat = $kunjunganHariIni;

                $antreanApotek = PharmacyQueue::where('clinic_queue_id', $kunjunganHariIni->id)
                    ->where('status', '!=', 'BATAL')
                    ->first();

                if (!in_array($kunjunganHariIni->status, ['SELESAI', 'BATAL'])) {
                    $antreanBerjalan = ClinicQueue::where('poli_id', $antreanBerobat->poli_id)
                        ->whereBetween('registration_time', [$startOfDay, $endOfDay])
                        ->where('status', 'DIPANGGIL')
                        ->orderBy('call_time', 'desc')
                        ->first();
                }
                
                if ($antreanApotek) {
                    $antreanApotekBerjalan = PharmacyQueue::whereBetween('created_at', [$startOfDay, $endOfDay])
                        ->where('status', 'SEDANG_DIRACIK')
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    
                    if ($antreanApotek->status == 'DALAM_ANTREAN') {
                        $jumlahAntreanApotekSebelumnya = PharmacyQueue::whereBetween('created_at', [$startOfDay, $endOfDay])
                            ->where('status', 'DALAM_ANTREAN')
                            ->where('created_at', '<', $antreanApotek->created_at)
                            ->count();
                    }
                }

            } else {
                // Riwayat terakhir
                $riwayatBerobatTerakhir = ClinicQueue::with(['poli', 'doctor.user'])
                    ->where('patient_id', $patient->id)
                    ->where(function ($query) {
                        $query->where('status', 'SELESAI')
                              ->orWhereHas('pharmacyQueue', function ($subQuery) {
                                  $subQuery->where('status', 'DITERIMA_PASIEN');
                              });
                    })
                    ->orderBy('finish_time', 'desc')
                    ->first();
            }
        }

        $polis = Poli::orderBy('name', 'asc')->get();
        $articles = Article::whereNotNull('published_at')
            ->latest('published_at')->take(3)->get();

        return view('pasien.dashboard', compact(
            'user', 'patient', 'antreanBerobat', 'riwayatBerobatTerakhir',
            'antreanBerjalan', 'antreanApotek', 'antreanApotekBerjalan',
            'jumlahAntreanApotekSebelumnya', 'polis', 'articles'
        ));
    }

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

            // --- PERBAIKAN LOGIKA WAKTU (STEP 2) ---
            // Kita gunakan Carbon::parse() biasa karena sistem sudah Asia/Jakarta
            $registrationStartOfDay = Carbon::parse($request->registration_date)->startOfDay();
            $registrationEndOfDay = Carbon::parse($request->registration_date)->endOfDay();

            // Cek antrean ganda
            $existingAntrean = ClinicQueue::where('patient_id', $patientForQueue->id)
                ->whereBetween('registration_time', [$registrationStartOfDay, $registrationEndOfDay])
                ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL'])
                ->exists();
            
            if ($existingAntrean) { return redirect()->back()->with('error', 'Pasien yang didaftarkan sudah memiliki antrean aktif untuk hari yang dipilih.'); }

            $poli = Poli::findOrFail($request->poli_id);
            $lastQueueCount = ClinicQueue::where('poli_id', $request->poli_id)
                ->whereBetween('registration_time', [$registrationStartOfDay, $registrationEndOfDay])
                ->count();
            $queueNumber = $poli->code . '-' . str_pad($lastQueueCount + 1, 3, '0', STR_PAD_LEFT);

            // --- PERBAIKAN PENTING DI SINI ---
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
                
                // KUNCI PERBAIKAN:
                // now() sekarang akan menghasilkan waktu 'Asia/Jakarta' (WIB).
                // created_at juga akan otomatis terisi waktu 'Asia/Jakarta'.
                // updated_at juga akan otomatis terisi waktu 'Asia/Jakarta'.
                'registration_time' => now(),
            ]);

            DB::commit();
            return redirect()->route('pasien.dashboard')->with('success', 'Pendaftaran antrean berhasil!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal membuat antrean: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server. Gagal membuat antrean.');
        }
    }

    // --- CONTOH LOGIKA UNTUK STATUS LAINNYA (CHECK-IN, DIPANGGIL, SELESAI) ---
    // Pastikan logika ini diterapkan di Controller/Method yang menangani perubahan status tersebut
    
    /* Contoh Method Check-In (Misalnya Scan QR):
    public function checkIn($id) {
        $queue = ClinicQueue::find($id);
        $queue->update([
            'status' => 'HADIR',
            'check_in_time' => now(), // Otomatis WIB
            // updated_at otomatis terupdate oleh Laravel
        ]);
    }

    Contoh Method Dokter Panggil Pasien:
    public function callPatient($id) {
        $queue = ClinicQueue::find($id);
        $queue->update([
            'status' => 'DIPANGGIL',
            'call_time' => now(), // Otomatis WIB
            // updated_at otomatis terupdate
        ]);
    }

    Contoh Method Selesai Periksa:
    public function finishCheckup($id) {
        $queue = ClinicQueue::find($id);
        $queue->update([
            'status' => 'SELESAI',
            'finish_time' => now(), // Otomatis WIB
            // updated_at otomatis terupdate
        ]);
    }
    */

    public function konfirmasiPenerimaanObat($pharmacyQueueId)
    {
        try {
            $antreanApotek = PharmacyQueue::findOrFail($pharmacyQueueId);
            $antreanKlinik = ClinicQueue::find($antreanApotek->clinic_queue_id);
            $pasien = Patient::find($antreanKlinik->patient_id);

            if ($pasien->user_id !== Auth::id()) {
                return redirect()->route('pasien.dashboard')->with('error', 'Akses tidak sah.');
            }

            DB::beginTransaction();

            $antreanApotek->update([
                'status' => 'DITERIMA_PASIEN',
                // now() sudah WIB, jadi taken_time akan akurat
                'taken_time' => now() 
            ]);

            Log::info("Rekam medis untuk antrean #{$antreanKlinik->id} telah dibuat setelah konfirmasi obat.");
            DB::commit();
            return redirect()->route('pasien.dashboard')->with('success', 'Konfirmasi penerimaan obat berhasil. Terima kasih.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal konfirmasi obat: ' . $e->getMessage());
            return redirect()->route('pasien.dashboard')->with('error', 'Terjadi kesalahan saat melakukan konfirmasi.');
        }
    }

    public function getDoctorsByPoli($poli_id)
    {
        Carbon::setLocale('id');
        // Tidak perlu hardcode timezone lagi, cukup now()
        $dayName = ucfirst(now()->dayName);
        
        $doctors = Doctor::where('poli_id', $poli_id)
            ->whereHas('doctorSchedules' , function ($query) use ($dayName) { 
                $query->where('day_of_week', $dayName)->where('is_active', true); 
            })
            ->with('user')
            ->get()
            ->map(function($doctor) { 
                return [ 'id' => $doctor->id, 'name' => $doctor->user->full_name ?? 'Dokter (Nama tidak tersedia)' ]; 
            });
        return response()->json($doctors);
    }
}