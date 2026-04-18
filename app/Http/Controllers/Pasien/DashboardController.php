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
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pasien.
     * Menggabungkan logika 'Clean' (Waktu & Antrean) dengan logika 'Buggy' (Billing).
     */
    public function index()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        // [FIX] Menggunakan now() agar sinkron dengan timezone aplikasi (Asia/Jakarta)
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        // Inisialisasi Variable
        $antreanBerobat = null;
        $riwayatBerobatTerakhir = null;
        $antreanBerjalan = null;
        $antreanApotek = null;
        $antreanApotekBerjalan = null;
        $jumlahAntreanApotekSebelumnya = 0;
        $tagihanObat = null; // Variable untuk billing

        if ($patient) {
            // 1. PRIORITAS UTAMA: Cari antrean yang sedang AKTIF (Menunggu/Hadir/Dipanggil)
            // Ini memperbaiki bug antrean sore tidak muncul karena tertimpa data pagi.
            $kunjunganHariIni = ClinicQueue::with(['poli', 'doctor.user'])
                ->where('patient_id', $patient->id)
                ->whereBetween('registration_time', [$startOfDay, $endOfDay])
                ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL'])
                ->orderBy('registration_time', 'desc') // Ambil yang paling baru jika ada duplikat aneh
                ->first();

            // 2. FALLBACK: Jika tidak ada antrean aktif, cari yang sudah SELESAI hari ini
            // Agar pasien bisa melihat status apotek/tagihan setelah keluar ruang dokter.
            if (!$kunjunganHariIni) {
                $kunjunganHariIni = ClinicQueue::with(['poli', 'doctor.user'])
                    ->where('patient_id', $patient->id)
                    ->whereBetween('registration_time', [$startOfDay, $endOfDay])
                    ->where('status', 'SELESAI')
                    ->orderBy('registration_time', 'desc')
                    ->first();
            }

            // Jika ditemukan kunjungan (Entah itu aktif atau selesai hari ini)
            if ($kunjunganHariIni) {
                $antreanBerobat = $kunjunganHariIni;

                // Cari Antrean Apotek terkait kunjungan ini
                $antreanApotek = PharmacyQueue::where('clinic_queue_id', $kunjunganHariIni->id)
                    ->where('status', '!=', 'BATAL')
                    ->first();

                // [LOGIKA BILLING DITAMBAHKAN KEMBALI DI SINI]
                // Ambil Rekam Medis -> Resep -> Tagihan
                $medicalRecord = MedicalRecord::where('clinic_queue_id', $kunjunganHariIni->id)->first();
                if ($medicalRecord) {
                    $tagihanObat = Prescription::where('medical_record_id', $medicalRecord->id)
                                    ->latest()
                                    ->first();
                }

                // Logika Estimasi Waktu Poli (Hanya jika belum selesai)
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
                // 3. HISTORY: Jika tidak ada sama sekali kunjungan hari ini, ambil riwayat terakhir
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
        // Force false karena fitur keluarga dinonaktifkan di blade
        $isFamilyRegistration = false;

        $baseRules = [
            'poli_id' => 'required|exists:polis,id',
            'doctor_id' => 'required|exists:doctors,id',
            'chief_complaint' => 'required|string|min:5|max:255',
            'registration_date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $baseRules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Terdapat kesalahan pada data yang Anda masukkan.');
        }

        try {
            DB::beginTransaction();

            // Ambil data pasien (Diri Sendiri)
            $patientForQueue = Patient::where('user_id', $user->id)->first();
            if (!$patientForQueue) {
                return redirect()->back()->with('error', 'Data profil pasien Anda tidak ditemukan.');
            }

            // Cek Double Antrean di Hari yang Sama (Hanya cek yg masih AKTIF)
            $registrationStartOfDay = Carbon::parse($request->registration_date)->startOfDay();
            $registrationEndOfDay = Carbon::parse($request->registration_date)->endOfDay();

            $existingAntrean = ClinicQueue::where('patient_id', $patientForQueue->id)
                ->whereBetween('registration_time', [$registrationStartOfDay, $registrationEndOfDay])
                ->whereIn('status', ['MENUNGGU', 'HADIR', 'DIPANGGIL'])
                ->exists();

            if ($existingAntrean) {
                return redirect()->back()->with('error', 'Anda sudah memiliki antrean aktif yang belum selesai hari ini.');
            }

            // Generate Nomor Antrean
            $poli = Poli::findOrFail($request->poli_id);
            $lastQueueCount = ClinicQueue::where('poli_id', $request->poli_id)
                ->whereBetween('registration_time', [$registrationStartOfDay, $registrationEndOfDay])
                ->count();
            $queueNumber = $poli->code . '-' . str_pad($lastQueueCount + 1, 3, '0', STR_PAD_LEFT);

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
                // [FIX CRITICAL] Gunakan now() agar jam tersimpan akurat (bukan 00:00:00)
                // Ini memastikan query whereBetween di index() bekerja dengan benar
                'registration_time' => now(),
            ]);

            DB::commit();
            return redirect()->route('pasien.dashboard')->with('success', 'Pendaftaran antrean berhasil!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal membuat antrean: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server. Gagal membuat antrean.');
        }
    }

    public function checkIn($qrCode)
    {
        try {
             // Validasi sederhana: QR Code diasumsikan berisi ID Antrean atau Kode Unik
             // Sesuaikan logika dekripsi jika QR code dienkripsi
             $antrean = ClinicQueue::find($qrCode); // Atau logic pencarian berdasarkan kode

             if (!$antrean) {
                 return response()->json(['success' => false, 'message' => 'Data antrean tidak ditemukan.']);
             }

             if ($antrean->status !== 'MENUNGGU') {
                 return response()->json(['success' => false, 'message' => 'Status antrean tidak valid untuk Check-In.']);
             }

             $antrean->update([
                 'status' => 'HADIR',
                 'check_in_time' => now()
             ]);

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

            if ($pasien->user_id !== Auth::id()) {
                return redirect()->route('pasien.dashboard')->with('error', 'Akses tidak sah.');
            }

            // Cek Pembayaran sebelum konfirmasi (Optional, double check di backend)
            // if ($tagihan && pending) return error...

            DB::beginTransaction();

            $antreanApotek->update([
                'status' => 'DITERIMA_PASIEN',
                'taken_time' => now()
            ]);

            Log::info("Obat diterima pasien antrean #{$antreanKlinik->id}");
            DB::commit();
            return redirect()->route('pasien.dashboard')->with('success', 'Konfirmasi penerimaan obat berhasil.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal konfirmasi obat: ' . $e->getMessage());
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
            })
            ->with('user')
            ->get()
            ->map(function($doctor) {
                return [ 'id' => $doctor->id, 'name' => $doctor->user->full_name ?? 'Dokter' ];
            });
        return response()->json($doctors);
    }
}