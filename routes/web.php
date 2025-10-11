<?php

use Illuminate\Support\Facades\Route;

// Import untuk Controller Autentikasi & Redirect
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardRedirectController;

// Import untuk Controller Role Spesifik dengan Alias untuk menghindari konflik nama
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboardController;
use App\Http\Controllers\Dokter\DashboardController as DokterDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda. Rute-rute
| ini dimuat oleh RouteServiceProvider dalam sebuah grup yang
| berisi grup middleware "web". Buat sesuatu yang hebat!
|
*/

Route::get('/', function () {
    return view('landing');
})->name('landing');


// == GRUP UNTTUK PENGGUNA YANG BELUM LOGIN (GUEST) ==
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// == GRUP UNTUK PENGGUNA YANG SUDAH LOGIN (AUTHENTICATED) ==
Route::middleware(['auth'])->group(function () {
    
    // Rute "Gerbang Utama" setelah login, akan diarahkan sesuai role
    Route::get('/dashboard', [DashboardRedirectController::class, 'index'])->name('dashboard');

    // Rute untuk proses Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // --- GRUP ROUTE UNTUK PASIEN ---
    Route::middleware(['role:pasien'])->prefix('pasien')->name('pasien.')->group(function () {
        Route::get('/dashboard', [PasienDashboardController::class, 'index'])->name('dashboard');
        Route::post('/antrean', [PasienDashboardController::class, 'store'])->name('antrean.store');
        Route::get('/doctors-by-poli/{poli_id}', [PasienDashboardController::class, 'getDoctorsByPoli'])->name('doctors.by.poli');
<<<<<<< Updated upstream
        // Tambahkan rute pasien lainnya di sini
=======
        Route::get('/check-in/{clinic_uuid}', [CheckInController::class, 'processCheckIn'])->name('queue.checkin');
            // Route::get('/antrean/check-in/{clinic_uuid}', [App\Http\Controllers\Pasien\CheckInController::class, 'processCheckIn'])->name('antrean.check-in');
>>>>>>> Stashed changes
    });

    // --- GRUP ROUTE UNTUK DOKTER ---
    Route::middleware(['role:dokter'])->prefix('dokter')->name('dokter.')->group(function () {
        Route::get('/dashboard', [DokterDashboardController::class, 'index'])->name('dashboard');
        Route::post('/antrean/{antrean}/panggil', [DokterDashboardController::class, 'panggilPasien'])->name('antrean.panggil');
        
        Route::post('/antrean/{antrean}/simpan-pemeriksaan', [DokterDashboardController::class, 'simpanPemeriksaan'])->name('antrean.simpanPemeriksaan');
<<<<<<< Updated upstream
        // Tambahkan rute dokter lainnya di sini
=======
        Route::post('/antrean-apotek/{antreanApotek}/konfirmasi', [App\Http\Controllers\Pasien\DashboardController::class, 'konfirmasiPenerimaanObat'])->name('antrean.apotek.konfirmasi');

>>>>>>> Stashed changes
    });

    // --- GRUP ROUTE UNTUK ADMIN ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        // Tambahkan rute admin lainnya di sini
    });

});

