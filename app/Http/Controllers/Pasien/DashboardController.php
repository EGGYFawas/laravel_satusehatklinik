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
        $today = Carbon::today()->toDateString();

        // Inisialisasi semua variabel yang akan dikirim ke view
        $antreanBerobat = null;
        $riwayatBerobatTerakhir = null;
        $antreanBerjalan = null;
        $antreanApotek = null;
        $antreanApotekBerjalan = null;
        $jumlahAntreanApotekSebelumnya = 0;

        if ($patient) {
            // 1. Cari kunjungan pasien hari ini, APAPUN STATUSNYA. Ini menjadi data acuan utama.
            $kunjunganHariIni = ClinicQueue::with(['poli', 'doctor.user'])
                ->where('patient_id', $patient->id)
                ->whereDate('registration_time', $today)
                ->first();

            if ($kunjunganHariIni) {
                // [MODIFIKASI UTAMA]
                // 2. Selalu isi '$antreanBerobat' jika ada kunjungan hari ini.
                // File Blade akan secara cerdas menentukan tampilan (aktif/selesai) berdasarkan status di dalamnya.
                $antreanBerobat = $kunjunganHariIni;

                // 3. Selalu cari antrean apotek yang terhubung.
                $antreanApotek = PharmacyQueue::where('clinic_queue_id', $kunjunganHariIni->id)
                    ->where('status', '!=', 'BATAL')
                    ->first();

                // 4. Ambil data pendukung (seperti estimasi) HANYA jika proses berobat masih berjalan.
                if (!in_array($kunjunganHariIni->status, ['SELESAI', 'BATAL'])) {
                    $antreanBerjalan = ClinicQueue::where('poli_id', $antreanBerobat->poli_id)
                        ->whereDate('registration_time', $today)
                        ->where('status', 'DIPANGGIL')
                        ->orderBy('call_time', 'desc')
                        ->first();
                }
                
                // 5. Jika ada antrean apotek, siapkan data pendukung untuk estimasinya.
                if ($antreanApotek) {
                    $antreanApotekBerjalan = PharmacyQueue::whereDate('created_at', $today)
                        ->where('status', 'SEDANG_DIRACIK')
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    
                    if ($antreanApotek->status == 'DALAM_ANTREAN') {
                        $jumlahAntreanApotekSebelumnya = PharmacyQueue::whereDate('created_at', $today)
                            ->where('status', 'DALAM_ANTREAN')
                            ->where('created_at', '<', $antreanApotek->created_at)
                            ->count();
                    }
                }

            } else {
                // 6. Jika TIDAK ADA kunjungan sama sekali hari ini, baru cari riwayat kunjungan terakhir.
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

        // Data lain yang tidak berubah
        $polis = Poli::orderBy('name', 'asc')->get();
        $articles = Article::whereNotNull('published_at')
            ->latest('published_at')->take(3)->get();

        // Kirim semua variabel yang dibutuhkan oleh view
        return view('pasien.dashboard', compact(
            'user',
            'patient',
            'antreanBerobat',
            'riwayatBerobatTerakhir',
            'antreanBerjalan',
            'antreanApotek',
            'antreanApotekBerjalan',
            'jumlahAntreanApotekSebelumnya',
            'polis',
            'articles'
        ));
    }
    
    /**
     * Menangani konfirmasi penerimaan obat oleh pasien. (Tidak Diubah)
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
    
    // ============================================================================================
    // == FUNGSI DI BAWAH INI TIDAK DIUBAH ==
    // ============================================================================================

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

    public function getDoctorsByPoli($poli_id)
    {
        Carbon::setLocale('id');
        $dayName = ucfirst(Carbon::now()->dayName);
        $doctors = Doctor::where('poli_id', $poli_id) ->whereHas('doctorSchedules' , function ($query) use ($dayName) { $query->where('day_of_week', $dayName)->where('is_active', true); }) ->with('user') ->get() ->map(function($doctor) { return [ 'id' => $doctor->id, 'name' => $doctor->user->full_name ?? 'Dokter (Nama tidak tersedia)' ]; });
        return response()->json($doctors);
    }
}

