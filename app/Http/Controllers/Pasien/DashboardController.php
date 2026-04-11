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
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Services\BpjsService; // [BARU] Import BpjsService
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| TAMBAHKAN ROUTE INI DI routes/web.php (Di dalam middleware role:pasien)
|--------------------------------------------------------------------------
|
| Route::get('/pasien/check-bpjs', [App\Http\Controllers\Pasien\DashboardController::class, 'checkBpjsStatus'])->name('pasien.check-bpjs');
|
*/

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pasien.
     */
    public function index()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        // Menggunakan now() agar sinkron dengan timezone aplikasi (Asia/Jakarta)
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        // Inisialisasi Variable
        $antreanBerobat = null;
        $riwayatBerobatTerakhir = null;
        $antreanBerjalan = null;
        $antreanApotek = null;
        $antreanApotekBerjalan = null;
        $jumlahAntreanApotekSebelumnya = 0;
        $tagihanObat = null;

        if ($patient) {
            // 1. PRIORITAS UTAMA: Cari antrean yang sedang AKTIF
            $kunjunganHariIni = ClinicQueue::with(['poli', 'doctor.user'])
                ->where('patient_id', $patient->id)
                ->whereBetween('registration_time', [$startOfDay, $endOfDay])
                ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL'])
                ->orderBy('registration_time', 'desc') 
                ->first();

            // 2. FALLBACK: Jika tidak ada antrean aktif, cari yang sudah SELESAI hari ini
            if (!$kunjunganHariIni) {
                $kunjunganHariIni = ClinicQueue::with(['poli', 'doctor.user'])
                    ->where('patient_id', $patient->id)
                    ->whereBetween('registration_time', [$startOfDay, $endOfDay])
                    ->where('status', 'SELESAI')
                    ->orderBy('registration_time', 'desc')
                    ->first();
            }

            // Jika ditemukan kunjungan
            if ($kunjunganHariIni) {
                $antreanBerobat = $kunjunganHariIni;

                $antreanApotek = PharmacyQueue::where('clinic_queue_id', $kunjunganHariIni->id)
                    ->where('status', '!=', 'BATAL')
                    ->first();

                // Ambil Rekam Medis -> Resep -> Tagihan
                $medicalRecord = MedicalRecord::where('clinic_queue_id', $kunjunganHariIni->id)->first();
                if ($medicalRecord) {
                    $tagihanObat = Prescription::where('medical_record_id', $medicalRecord->id)
                                    ->latest()
                                    ->first();
                }

                // Logika Estimasi Waktu Poli
                if (!in_array($kunjunganHariIni->status, ['SELESAI', 'BATAL'])) {
                    $antreanBerjalan = ClinicQueue::where('poli_id', $antreanBerobat->poli_id)
                        ->whereBetween('registration_time', [$startOfDay, $endOfDay])
                        ->where('status', 'DIPANGGIL')
                        ->orderBy('call_time', 'desc')
                        ->first();
                }

                // Logika Estimasi Waktu Apotek
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
                // 3. HISTORY: Ambil riwayat terakhir
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
            'jumlahAntreanApotekSebelumnya', 'polis', 'articles', 'tagihanObat'
        ));
    }

    /**
     * Memproses pendaftaran antrean baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $isFamilyRegistration = false;

        $baseRules = [
            'poli_id' => 'required|exists:polis,id',
            'doctor_id' => 'required|exists:doctors,id',
            'chief_complaint' => 'required|string|min:5|max:255',
            'registration_date' => 'required|date',
            'payment_method' => 'required|in:Umum,BPJS', // [BARU] Validasi Cara Bayar
        ];

        $validator = Validator::make($request->all(), $baseRules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Terdapat kesalahan pada data yang Anda masukkan.');
        }

        try {
            DB::beginTransaction();

            $patientForQueue = Patient::where('user_id', $user->id)->first();
            if (!$patientForQueue) {
                return redirect()->back()->with('error', 'Data profil pasien Anda tidak ditemukan.');
            }

            // Cek Double Antrean di Hari yang Sama
            $registrationStartOfDay = Carbon::parse($request->registration_date)->startOfDay();
            $registrationEndOfDay = Carbon::parse($request->registration_date)->endOfDay();

            $existingAntrean = ClinicQueue::where('patient_id', $patientForQueue->id)
                ->whereBetween('registration_time', [$registrationStartOfDay, $registrationEndOfDay])
                ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL'])
                ->exists();

            if ($existingAntrean) {
                return redirect()->back()->with('error', 'Anda sudah memiliki antrean aktif yang belum selesai hari ini.');
            }

            // [BARU] Validasi Ulang jika memilih BPJS (Keamanan Ganda)
            if ($request->payment_method === 'BPJS') {
                $bpjsService = new BpjsService();
                $cekBpjs = $bpjsService->getPesertaByNIK($patientForQueue->nik);
                
                if (!$cekBpjs['success'] || strpos(strtoupper($cekBpjs['data']['statusPeserta']['keterangan'] ?? $cekBpjs['data']['status'] ?? ''), 'AKTIF') === false) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'Status BPJS Anda tidak aktif atau tidak ditemukan. Silakan pilih jalur UMUM.');
                }
                
                // Simpan nomor kartu BPJS ke database jika belum ada
                $noKartu = $cekBpjs['data']['noKartu'] ?? $cekBpjs['data']['no_kartu'] ?? null;
                if ($noKartu && empty($patientForQueue->bpjs_number)) {
                    $patientForQueue->update(['bpjs_number' => $noKartu]);
                }
            }

            // Generate Nomor Antrean
            $poli = Poli::findOrFail($request->poli_id);
            $lastQueueCount = ClinicQueue::where('poli_id', $request->poli_id)
                ->whereBetween('registration_time', [$registrationStartOfDay, $registrationEndOfDay])
                ->count();
            
            // Format prefix poli, misal Poli Umum (U)
            $prefix = $poli->prefix ?? strtoupper(substr($poli->name, 0, 1));
            $queueNumber = $prefix . '-' . str_pad($lastQueueCount + 1, 3, '0', STR_PAD_LEFT);

            // Simpan Antrean
            ClinicQueue::create([
                'patient_id' => $patientForQueue->id,
                'poli_id' => $request->poli_id,
                'doctor_id' => $request->doctor_id,
                'registered_by_user_id' => $user->id,
                'queue_number' => $queueNumber,
                'chief_complaint' => $request->chief_complaint,
                'patient_relationship' => 'Diri Sendiri',
                'status' => 'MENUNGGU',
                'payment_method' => $request->payment_method, // [BARU] Simpan Cara Bayar
                'registration_time' => now(),
            ]);

            DB::commit();
            return redirect()->route('pasien.dashboard')->with('success', 'Pendaftaran antrean berhasil menggunakan jalur ' . $request->payment_method . '!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal membuat antrean: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server. Gagal membuat antrean.');
        }
    }

    /**
     * [BARU] API untuk Pasien mengecek BPJS mereka sendiri
     */
    public function checkBpjsStatus()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();
        
        if (!$patient || empty($patient->nik)) {
            return response()->json(['success' => false, 'message' => 'NIK tidak ditemukan di profil Anda.']);
        }

        $bpjsService = new BpjsService();
        $response = $bpjsService->getPesertaByNIK($patient->nik);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'data' => $response['data'] 
            ]);
        }

        return response()->json(['success' => false, 'message' => $response['message']]);
    }

    // ... sisa method (checkIn, konfirmasiPenerimaanObat, getDoctorsByPoli) tetap dibiarkan sama.
    // Untuk mempersingkat respon agar tidak kepotong, paste 3 fungsi lo yang lama di sini ya.
    
    public function checkIn($qrCode)
    {
        try {
            $antrean = ClinicQueue::find($qrCode);
            if (!$antrean) return response()->json(['success' => false, 'message' => 'Data antrean tidak ditemukan.']);
            if ($antrean->status !== 'MENUNGGU') return response()->json(['success' => false, 'message' => 'Status antrean tidak valid untuk Check-In.']);
            
            $antrean->update(['status' => 'HADIR', 'check_in_time' => now()]);
            return response()->json(['success' => true, 'message' => 'Anda berhasil Check-In. Silakan tunggu panggilan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
        }
    }

    public function konfirmasiPenerimaanObat($pharmacyQueueId)
    {
        try {
            $antreanApotek = PharmacyQueue::findOrFail($pharmacyQueueId);
            $antreanKlinik = ClinicQueue::find($antreanApotek->clinic_queue_id);
            $pasien = Patient::find($antreanKlinik->patient_id);

            if ($pasien->user_id !== Auth::id()) return redirect()->route('pasien.dashboard')->with('error', 'Akses tidak sah.');

            DB::beginTransaction();
            $antreanApotek->update(['status' => 'DITERIMA_PASIEN', 'taken_time' => now()]);
            DB::commit();
            return redirect()->route('pasien.dashboard')->with('success', 'Konfirmasi penerimaan obat berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pasien.dashboard')->with('error', 'Terjadi kesalahan saat melakukan konfirmasi.');
        }
    }

    public function getDoctorsByPoli($poli_id)
    {
        Carbon::setLocale('id');
        $dayName = ucfirst(now()->dayName);
        $doctors = Doctor::where('poli_id', $poli_id)
            ->whereHas('doctorSchedules' , function ($query) use ($dayName) {
                $query->where('day_of_week', $dayName)->where('is_active', true);
            })->with('user')->get()->map(function($doctor) {
                return [ 'id' => $doctor->id, 'name' => $doctor->user->full_name ?? 'Dokter' ];
            });
        return response()->json($doctors);
    }
}