<?php

use Illuminate\Support\Facades\Route;

// Import untuk Controller Autentikasi & Redirect
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardRedirectController;

// Import untuk Controller Role Spesifik dengan Alias
// use App\Http\Controllers\Admin\DashboardController as AdminDashboardController; // Dihapus karena Filament
use App\Http\Controllers\Pasien\DashboardController as PasienDashboardController;
use App\Http\Controllers\Dokter\DashboardController as DokterDashboardController;
use App\Http\Controllers\Pasien\CheckInController;
use App\Http\Controllers\PetugasLoket\DashboardController as PetugasLoketDashboardController;
use App\Http\Controllers\PetugasLoket\AntreanOfflineController;
use App\Http\Controllers\Pasien\ProfileController as PasienProfileController;
use App\Http\Controllers\Pasien\HistoryController as PasienHistoryController;
use App\Http\Controllers\Dokter\PatientHistoryController as DokterPatientHistoryController;
use App\Http\Controllers\Pasien\ScheduleController as PasienScheduleController;
use App\Http\Controllers\Dokter\ScheduleController as DokterScheduleController;
use App\Http\Controllers\Pasien\ArticleController as PasienArticleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\LandingPageController; // <--- TAMBAHKAN INI di bagian atas

Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// == GRUP UNTUK PENGGUNA YANG BELUM LOGIN (GUEST) ==
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('/check-patient-nik-public/{nik}', [AuthController::class, 'checkPatientPublic'])->name('check-patient-nik-public');
});

// == GRUP UNTUK PENGGUNA YANG SUDAH LOGIN (AUTHENTICATED) ==
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardRedirectController::class, 'index'])->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // --- GRUP ROUTE UNTUK PASIEN ---
    Route::middleware(['role:pasien'])->prefix('pasien')->name('pasien.')->group(function () {
        Route::get('/dashboard', [PasienDashboardController::class, 'index'])->name('dashboard');
        Route::post('/antrean', [PasienDashboardController::class, 'store'])->name('antrean.store');
        Route::get('/doctors-by-poli/{poli_id}', [PasienDashboardController::class, 'getDoctorsByPoli'])->name('doctors.by.poli');
        Route::get('/check-in/{clinic_uuid}', [CheckInController::class, 'processCheckInAjax'])->name('checkin.ajax');
        Route::post('/antrean/apotek/{pharmacyQueueId}/konfirmasi', [PasienDashboardController::class, 'konfirmasiPenerimaanObat'])->name('antrean.apotek.konfirmasi');
        Route::get('/profil', [PasienProfileController::class, 'show'])->name('profil.show');
        Route::put('/profil', [PasienProfileController::class, 'update'])->name('profil.update');
        Route::get('/profil/edit', [PasienProfileController::class, 'edit'])->name('profil.edit');
        Route::get('/riwayat', [PasienHistoryController::class, 'index'])->name('riwayat.index');
        Route::get('/riwayat/{patient}', [PasienHistoryController::class, 'show'])->name('riwayat.show');
        Route::get('/jadwal-dokter', [PasienScheduleController::class, 'index'])->name('jadwal.index');
        Route::get('/artikel', [PasienArticleController::class, 'index'])->name('artikel.index');
        Route::get('/artikel/{article:slug}', [PasienArticleController::class, 'show'])->name('artikel.show');
    });

    // --- GRUP ROUTE UNTUK DOKTER ---
    Route::middleware(['role:dokter'])->prefix('dokter')->name('dokter.')->group(function () {
        Route::get('/dashboard', [DokterDashboardController::class, 'index'])->name('dashboard');
        Route::post('/antrean/{antrean}/panggil', [DokterDashboardController::class, 'panggilPasien'])->name('antrean.panggil');
        
        // [MODIFIKASI UTAMA] Memperbaiki typo 'simpanPamemeriksaan'
        Route::post('/antrean/{antrean}/simpan-pemeriksaan', [DokterDashboardController::class, 'simpanPemeriksaan'])->name('antrean.simpanPemeriksaan');
        
        Route::get('/riwayat-pasien', [DokterPatientHistoryController::class, 'index'])->name('riwayat-pasien.index');
        Route::get('/riwayat-pasien/{patient}', [DokterPatientHistoryController::class, 'show'])->name('riwayat-pasien.show');
        Route::get('/jadwal-saya', [DokterScheduleController::class, 'index'])->name('jadwal.index');
       Route::get('/icd10/search', [App\Http\Controllers\Dokter\DashboardController::class, 'searchIcd10'])->name('icd10.search');
    });

    // Grup Admin dihapus karena sudah ditangani oleh Filament

    // --- GRUP ROUTE UNTUK PETUGAS LOKET ---
    Route::middleware(['role:petugas loket'])->prefix('petugas-loket')->name('petugas-loket.')->group(function () {
        Route::get('/dashboard', [PetugasLoketDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/antrean-apotek/{pharmacyQueue}/update-status', [PetugasLoketDashboardController::class, 'updateStatus'])->name('antrean-apotek.updateStatus');
        Route::get('/antrean-offline', [AntreanOfflineController::class, 'index'])->name('antrean-offline.index');
        Route::post('/antrean-offline', [AntreanOfflineController::class, 'store'])->name('antrean-offline.store');
        Route::patch('/antrean-offline/{antrean}/check-in', [AntreanOfflineController::class, 'checkIn'])->name('antrean-offline.checkin');
        Route::get('/doctors-by-poli/{poli}', [AntreanOfflineController::class, 'getDoctorsByPoli'])->name('doctors.by.poli');
        Route::get('/check-patient-nik/{nik}', [AntreanOfflineController::class, 'checkPatientByNIK'])->name('check-patient-nik');
    });

});

