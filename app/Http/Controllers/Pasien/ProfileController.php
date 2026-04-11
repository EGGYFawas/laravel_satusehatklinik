<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Patient;
use App\Models\User;
use App\Services\SatuSehatService; // WAJIB DI-IMPORT

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pasien.
     */
    public function show()
    {
        $user = Auth::user();
        $patient = Patient::firstOrCreate(
            ['user_id' => $user->id],
            ['full_name' => $user->full_name]
        );
        return view('pasien.profil', compact('user', 'patient'));
    }

    /**
     * Memperbarui data profil menggunakan Validator::make() untuk kontrol penuh.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $patient = $user->patient;

        // Aturan validasi dasar
        $rules = [
            'full_name' => 'required|string|max:100',
            'nik' => ['required', 'string', 'digits:16', Rule::unique('patients')->ignore($patient->id)],
            'phone_number' => 'nullable|string|max:15|regex:/^08[0-9]{8,11}$/',
            'address' => 'nullable|string|max:255',
        ];

        // Aturan validasi kondisional untuk data akun
        if ($request->input('change_account') === 'true') {
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)];
            $rules['password'] = ['nullable', 'confirmed', Password::min(6)];
        }

        // Pesan error kustom
        $messages = [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 angka.',
            'nik.unique' => 'NIK ini sudah terdaftar.',
            'phone_number.regex' => 'Format nomor telepon tidak valid (contoh: 081234567890).',
            'email.required' => 'Email baru wajib diisi untuk mengubah data akun.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru harus minimal 6 karakter.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', true); 
        }

        $validatedData = $validator->validated();

        DB::beginTransaction();
        try {
            // 1. Update data di tabel 'patients'
            $patient->update([
                'full_name' => strtoupper($validatedData['full_name']),
                'nik' => $validatedData['nik'],
                'phone_number' => $validatedData['phone_number'] ?? null,
                'address' => $validatedData['address'] ?? null,
            ]);

            // 2. Update data di tabel 'users'
            $user->full_name = strtoupper($validatedData['full_name']);

            if ($request->input('change_account') === 'true') {
                $user->email = $validatedData['email'];
                if (!empty($validatedData['password'])) {
                    $user->password = Hash::make($validatedData['password']);
                }
            }
            
            $user->save();

            DB::commit();

            return redirect()->route('pasien.profil.show')->with('success', 'Profil Anda berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update profil pasien: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan pada server. Gagal memperbarui profil.');
        }
    }

    // =========================================================================
    // 3. BAGIAN CEK NIK (AJAX UNTUK FRONTEND & SATUSEHAT INJECTION)
    // =========================================================================

    /**
     * Mengecek data pasien berdasarkan NIK (Lokal -> Kemenkes)
     */
    public function checkPatientPublic($nik)
    {
        $validator = Validator::make(['nik' => $nik], [
            'nik' => 'required|string|digits:16'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'NIK tidak valid.'], 400);
        }

        // 1. CEK KE DATABASE LOKAL DULU (Data Walk-in Loket)
        $patient = Patient::where('nik', $nik)->first();

        if ($patient) {
            $patientData = [
                'full_name' => $patient->full_name,
                'date_of_birth' => $patient->date_of_birth,
                'gender' => $patient->gender,
                'ihs_number' => $patient->ihs_number ?? null // Ambil IHS jika sudah ada di DB lokal
            ];

            return response()->json([
                'found' => true,
                'is_satusehat' => false, // Flag bahwa ini data lokal
                'has_account' => $patient->user_id ? true : false,
                'data' => $patientData
            ]);
        }

        // 2. JIKA LOKAL TIDAK ADA, TEMBAK API SATUSEHAT KEMENKES!
        // Instansiasi Service (Pastikan file Service yang gue kasih di response sblmnya udah lo buat)
        $satuSehat = new SatuSehatService();
        $kemenkesResponse = $satuSehat->getPatientByNIK($nik);

        if ($kemenkesResponse['success']) {
            $satusehatData = $kemenkesResponse['data'];

            // Format gender Kemenkes (male/female) ke format DB kita
            $genderLokal = '';
            if (strtolower($satusehatData['gender']) == 'male') $genderLokal = 'Laki-laki';
            if (strtolower($satusehatData['gender']) == 'female') $genderLokal = 'Perempuan';

            return response()->json([
                'found' => true,
                'is_satusehat' => true, // Flag penting buat frontend
                'has_account' => false, // Karena dari Kemenkes, pasti belum punya akun di lokal
                'data' => [
                    'full_name' => strtoupper($satusehatData['name']),
                    'date_of_birth' => $satusehatData['birthDate'],
                    'gender' => $genderLokal,
                    'ihs_number' => $satusehatData['ihs_number'] // Wajib dikirim ke view
                ]
            ]);
        }

        // 3. JIKA LOKAL KOSONG & KEMENKES KOSONG
        return response()->json([
            'found' => false,
            'message' => $kemenkesResponse['message'] ?? 'Data tidak ditemukan.'
        ]);
    }
}