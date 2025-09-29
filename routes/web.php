<?php

use Illuminate\Support\Facades\Route;

// Import untuk Controller Aplikasi
use App\Http\Controllers\AppInfoController;
use App\Http\Controllers\BayarController;
// ... (sisa use statement Anda yang lain tetap sama)
use App\Http\Controllers\TindakanController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PractitionerController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\SatusehatController;
use App\Http\Controllers\BundleController;


// Import untuk Controller Autentikasi & Redirect
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardRedirectController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// MODIFIKASI: Langsung tampilkan view landing page di route utama
// dan berikan nama 'landing' untuk mengatasi error "Route [landing] not defined".
Route::get('/', function () {
    // Pastikan Anda memiliki file view di resources/views/landing.blade.php
    // Jika file Anda masih bernama welcome.blade.php, ganti 'landing' menjadi 'welcome'
    return view('landing');
})->name('landing');


// == GRUP UNTUK TAMU (PENGGUNA YANG BELUM LOGIN) ==
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// == GRUP UNTUK PENGGUNA YANG SUDAH LOGIN ==
Route::middleware(['auth'])->group(function () {
    
    // Rute "Pintu Gerbang" setelah login
    Route::get('/dashboard', [DashboardRedirectController::class, 'index'])->name('dashboard');

    // Rute Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // --- GRUP ROUTE UNTUK PASIEN ---
    // PERBAIKAN: Gunakan middleware 'role' dari Spatie, bukan CheckRole::class
    Route::middleware(['role:pasien'])->prefix('pasien')->name('pasien.')->group(function () {
        Route::get('/dashboard', function () {
            return view('pasien.dashboard'); 
        })->name('dashboard');
    });

    // --- GRUP ROUTE UNTUK ADMIN / STAF KLINIK ---
    // PERBAIKAN: Gunakan middleware 'role' dari Spatie
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        
        // ... (sisa rute admin Anda tetap sama)
        Route::resource('pasien', App\Http\Controllers\PasienController::class);
        Route::resource('kunjungan', App\Http\Controllers\PendaftaranController::class);
        // ... dan seterusnya
    });

    // Anda bisa menambahkan grup lain untuk role 'dokter', 'petugas loket apotek', dll.
    // Contoh:
    // Route::middleware(['role:dokter'])->prefix('dokter')->name('dokter.')->group(function () {
    //      // Rute-rute dokter
    // });
});
