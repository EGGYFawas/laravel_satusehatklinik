<?php

use Illuminate\Support\Facades\Route;

// Import untuk Controller Autentikasi & Redirect
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardRedirectController;

// Import untuk Controller Role Spesifik dengan Alias
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboardController;
use App\Http\Controllers\Dokter\DashboardController as DokterDashboardController;
use App\Http\Controllers\Pasien\CheckInController;
use App\Http\Controllers\PetugasLoket\DashboardController as PetugasLoketDashboardController;
use App\Http\Controllers\PetugasLoket\AntreanOfflineController;
use App\Http\Controllers\Pasien\ProfileController as PasienProfileController;
use App\Http\Controllers\Pasien\HistoryController as PasienHistoryController;
use App\Http\Controllers\Dokter\PatientHistoryController as DokterPatientHistoryController;
// [PENAMBAHAN BARU] Import Schedule Controllers
use App\Http\Controllers\Pasien\ScheduleController as PasienScheduleController;
use App\Http\Controllers\Dokter\ScheduleController as DokterScheduleController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing');
})->name('landing');


// == GRUP UNTUK PENGGUNA YANG BELUM LOGIN (GUEST) ==
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
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

        // [PENAMBAHAN BARU] Route untuk Jadwal Dokter (Pasien)
        Route::get('/jadwal-dokter', [PasienScheduleController::class, 'index'])->name('jadwal.index');
    });

    // --- GRUP ROUTE UNTUK DOKTER ---
    Route::middleware(['role:dokter'])->prefix('dokter')->name('dokter.')->group(function () {
        Route::get('/dashboard', [DokterDashboardController::class, 'index'])->name('dashboard');
        Route::post('/antrean/{antrean}/panggil', [DokterDashboardController::class, 'panggilPasien'])->name('antrean.panggil');
        Route::post('/antrean/{antrean}/simpan-pemeriksaan', [DokterDashboardController::class, 'simpanPemeriksaan'])->name('antrean.simpanPemeriksaan');
        Route::get('/riwayat-pasien', [DokterPatientHistoryController::class, 'index'])->name('riwayat-pasien.index');
        Route::get('/riwayat-pasien/{patient}', [DokterPatientHistoryController::class, 'show'])->name('riwayat-pasien.show');

        // [PENAMBAHAN BARU] Route untuk Jadwal Saya (Dokter)
        Route::get('/jadwal-saya', [DokterScheduleController::class, 'index'])->name('jadwal.index');
        // Nanti bisa ditambahkan route POST/PUT untuk update jadwal jika diperlukan
     });

    // --- GRUP ROUTE UNTUK ADMIN ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    // --- GRUP ROUTE UNTUK PETUGAS LOKET ---
    Route::middleware(['role:petugas loket'])->prefix('petugas-loket')->name('petugas-loket.')->group(function () {
        Route::get('/dashboard', [PetugasLoketDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/antrean-apotek/{pharmacyQueue}/update-status', [PetugasLoketDashboardController::class, 'updateStatus'])->name('antrean-apotek.updateStatus');
        Route::get('/antrean-offline', [AntreanOfflineController::class, 'index'])->name('antrean-offline.index');
        Route::post('/antrean-offline', [AntreanOfflineController::class, 'store'])->name('antrean-offline.store');
        Route::patch('/antrean-offline/{antrean}/check-in', [AntreanOfflineController::class, 'checkIn'])->name('antrean-offline.checkin');
        Route::get('/doctors-by-poli/{poli}', [AntreanOfflineController::class, 'getDoctorsByPoli'])->name('doctors.by.poli');
    });

});

