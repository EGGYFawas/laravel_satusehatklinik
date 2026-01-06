<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest; // [PENTING] Tambahkan ini untuk verifikasi email

// ==============================================================================
// 1. IMPORT CONTROLLERS
// ==============================================================================

// Controller Autentikasi & Umum
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\InvoiceController;

// Controller Pasien
use App\Http\Controllers\Pasien\DashboardController as PasienDashboardController;
use App\Http\Controllers\Pasien\CheckInController;
use App\Http\Controllers\Pasien\ProfileController as PasienProfileController;
use App\Http\Controllers\Pasien\HistoryController as PasienHistoryController;
use App\Http\Controllers\Pasien\ScheduleController as PasienScheduleController;
use App\Http\Controllers\Pasien\ArticleController as PasienArticleController;
use App\Http\Controllers\Pasien\BillingController;

// Controller Dokter
use App\Http\Controllers\Dokter\DashboardController as DokterDashboardController;
use App\Http\Controllers\Dokter\PatientHistoryController as DokterPatientHistoryController;
use App\Http\Controllers\Dokter\ScheduleController as DokterScheduleController;

// Controller Petugas Loket
use App\Http\Controllers\PetugasLoket\DashboardController as PetugasLoketDashboardController;
use App\Http\Controllers\PetugasLoket\AntreanOfflineController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==============================================================================
// 2. PUBLIC ROUTES (Bisa diakses Guest & User Login)
// ==============================================================================

// Landing Page
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Artikel Publik
Route::get('/artikel', [ArticleController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{article:slug}', [ArticleController::class, 'show'])->name('artikel.show');


// ==============================================================================
// 3. GUEST ROUTES (Hanya untuk yang BELUM Login)
// ==============================================================================
Route::middleware('guest')->group(function () {
    // Login & Register
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('/check-patient-nik-public/{nik}', [AuthController::class, 'checkPatientPublic'])->name('check-patient-nik-public');

    // [BARU] Lupa Password (Input Email)
    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

    // [BARU] Reset Password (Input Password Baru)
    // Route ini menangani link yang diklik dari email
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.passwords.reset', ['token' => $token]);
    })->name('password.reset');
    
    // Note: Pastikan method resetPassword ada di AuthController jika ingin fitur submit password baru berfungsi penuh
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});


// ==============================================================================
// 4. AUTHENTICATED ROUTES (Harus Login)
// ==============================================================================
Route::middleware(['auth'])->group(function () {
    
    // Redirect Dashboard & Logout
    Route::get('/dashboard', [DashboardRedirectController::class, 'index'])->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // [BARU] Verifikasi Email Routes
    // 1. Tampilan "Harap Verifikasi Email"
    Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])
        ->name('verification.notice');

    // 2. Handler saat Link di Email diklik
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard')->with('success', 'Email berhasil diverifikasi! Selamat datang.');
    })->middleware(['signed'])->name('verification.verify');

    // 3. Kirim Ulang Link Verifikasi
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');


    // [BARU] Route Global untuk Download Struk
    Route::get('/invoice/{id}/download', [InvoiceController::class, 'download'])->name('invoice.download');

    // --- GROUP: ROLE PASIEN ---
    Route::middleware(['role:pasien'])->prefix('pasien')->name('pasien.')->group(function () {
        Route::get('/dashboard', [PasienDashboardController::class, 'index'])->name('dashboard');
        Route::post('/antrean', [PasienDashboardController::class, 'store'])->name('antrean.store');
        Route::get('/doctors-by-poli/{poli_id}', [PasienDashboardController::class, 'getDoctorsByPoli'])->name('doctors.by.poli');
        Route::get('/check-in/{clinic_uuid}', [CheckInController::class, 'processCheckInAjax'])->name('checkin.ajax');
        Route::post('/antrean/apotek/{pharmacyQueueId}/konfirmasi', [PasienDashboardController::class, 'konfirmasiPenerimaanObat'])->name('antrean.apotek.konfirmasi');
        
        // Profil
        Route::get('/profil', [PasienProfileController::class, 'show'])->name('profil.show');
        Route::put('/profil', [PasienProfileController::class, 'update'])->name('profil.update');
        Route::get('/profil/edit', [PasienProfileController::class, 'edit'])->name('profil.edit');
        
        // Riwayat & Jadwal
        Route::get('/riwayat', [PasienHistoryController::class, 'index'])->name('riwayat.index');
        Route::get('/riwayat/{patient}', [PasienHistoryController::class, 'show'])->name('riwayat.show');
        Route::get('/jadwal-dokter', [PasienScheduleController::class, 'index'])->name('jadwal.index');

        // Artikel
        Route::get('/artikel', [PasienArticleController::class, 'index'])->name('artikel.index');
        Route::get('/artikel/{article:slug}', [PasienArticleController::class, 'show'])->name('artikel.show');
        
        // Billing
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/pay/{prescription}', [BillingController::class, 'pay'])->name('billing.pay');
        Route::get('/billing/check/{prescription}', [BillingController::class, 'checkStatus'])->name('billing.check');
    });

    // --- GROUP: ROLE DOKTER ---
    Route::middleware(['role:dokter'])->prefix('dokter')->name('dokter.')->group(function () {
        Route::get('/dashboard', [DokterDashboardController::class, 'index'])->name('dashboard');
        Route::post('/antrean/{antrean}/panggil', [DokterDashboardController::class, 'panggilPasien'])->name('antrean.panggil');
        Route::post('/antrean/{antrean}/simpan-pemeriksaan', [DokterDashboardController::class, 'simpanPemeriksaan'])->name('antrean.simpanPemeriksaan');
        Route::get('/riwayat-pasien', [DokterPatientHistoryController::class, 'index'])->name('riwayat-pasien.index');
        Route::get('/riwayat-pasien/{patient}', [DokterPatientHistoryController::class, 'show'])->name('riwayat-pasien.show');
        Route::get('/jadwal-saya', [DokterScheduleController::class, 'index'])->name('jadwal.index');
        Route::get('/icd10/search', [DokterDashboardController::class, 'searchIcd10'])->name('icd10.search');
    });

    // --- GROUP: ROLE PETUGAS LOKET ---
    Route::middleware(['role:petugas loket'])->prefix('petugas-loket')->name('petugas-loket.')->group(function () {
        Route::get('/dashboard', [PetugasLoketDashboardController::class, 'index'])->name('dashboard');
        
        // Update Status
        Route::patch('/antrean-apotek/{pharmacyQueue}/update-status', [PetugasLoketDashboardController::class, 'updateStatus'])->name('antrean-apotek.updateStatus');
        
        // Bayar Tunai Manual
        Route::post('/bayar-tunai/{pharmacyQueueId}', [PetugasLoketDashboardController::class, 'bayarTunai'])->name('bayar-tunai');
        Route::post('/cek-status-bayar/{pharmacyQueueId}', [PetugasLoketDashboardController::class, 'checkPaymentStatus'])->name('cek-status-bayar');

        // Antrean Offline
        Route::get('/antrean-offline', [AntreanOfflineController::class, 'index'])->name('antrean-offline.index');
        Route::post('/antrean-offline', [AntreanOfflineController::class, 'store'])->name('antrean-offline.store');
        Route::patch('/antrean-offline/{antrean}/check-in', [AntreanOfflineController::class, 'checkIn'])->name('antrean-offline.checkin');
        Route::get('/doctors-by-poli/{poli}', [AntreanOfflineController::class, 'getDoctorsByPoli'])->name('doctors.by.poli');
        Route::get('/check-patient-nik/{nik}', [AntreanOfflineController::class, 'checkPatientByNIK'])->name('check-patient-nik');
    });
});