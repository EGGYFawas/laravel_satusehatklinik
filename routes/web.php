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
        
        // Route untuk proses Check-In via QR Code (tidak diubah)
        Route::get('/check-in/{clinic_uuid}', [CheckInController::class, 'processCheckInAjax'])->name('checkin.ajax');

        // [BARU] Menambahkan route untuk menangani konfirmasi penerimaan obat oleh pasien
        Route::post('/antrean/apotek/{pharmacyQueueId}/konfirmasi', [PasienDashboardController::class, 'konfirmasiPenerimaanObat'])->name('antrean.apotek.konfirmasi');
    });

    // --- GRUP ROUTE UNTUK DOKTER ---
    Route::middleware(['role:dokter'])->prefix('dokter')->name('dokter.')->group(function () {
        Route::get('/dashboard', [DokterDashboardController::class, 'index'])->name('dashboard');
        Route::post('/antrean/{antrean}/panggil', [DokterDashboardController::class, 'panggilPasien'])->name('antrean.panggil');
        Route::post('/antrean/{antrean}/simpan-pemeriksaan', [DokterDashboardController::class, 'simpanPemeriksaan'])->name('antrean.simpanPemeriksaan');
    });

    // --- GRUP ROUTE UNTUK ADMIN ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    // --- GRUP ROUTE UNTUK PETUGAS LOKET ---
    Route::middleware(['role:petugas loket'])->prefix('petugas-loket')->name('petugas-loket.')->group(function () {
        // Rute untuk menampilkan dashboard utama apotek
        Route::get('/dashboard', [PetugasLoketDashboardController::class, 'index'])->name('dashboard');

        // Rute untuk aksi-aksi yang dilakukan petugas
        Route::post('/antrean-apotek/{pharmacyQueue}/mulai-racik', [PetugasLoketDashboardController::class, 'startRacik'])->name('antrean-apotek.startRacik');
        Route::post('/antrean-apotek/{pharmacyQueue}/selesai-racik', [PetugasLoketDashboardController::class, 'finishRacik'])->name('antrean-apotek.finishRacik');
        Route::post('/antrean-apotek/{pharmacyQueue}/serahkan-obat', [PetugasLoketDashboardController::class, 'markAsTaken'])->name('antrean-apotek.markAsTaken');
    });

});
