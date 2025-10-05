<?php

use Illuminate\Support\Facades\Route;

// Import untuk Controller Aplikasi
use App\Http\Controllers\AppInfoController;
use App\Http\Controllers\BundleController;

// Import untuk Controller Autentikasi & Redirect
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardRedirectController;

// Import untuk Controller Role Spesifik
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboardController; // Import controller pasien


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
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
    
    // Rute "Pintu Gerbang" setelah login yang akan diarahkan oleh DashboardRedirectController
    Route::get('/dashboard', [DashboardRedirectController::class, 'index'])->name('dashboard');

    // Rute Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // --- GRUP ROUTE UNTUK PASIEN (VERSI FINAL) ---
    Route::middleware(['role:pasien'])->prefix('pasien')->name('pasien.')->group(function () {
        // Rute untuk menampilkan halaman dashboard utama
        Route::get('/dashboard', [PasienDashboardController::class, 'index'])->name('dashboard');
        
        // Rute untuk menyimpan data antrean baru dari form
        Route::post('/antrean', [PasienDashboardController::class, 'store'])->name('antrean.store');
        
        // Rute API internal untuk mengambil daftar dokter berdasarkan poli yang dipilih
        Route::get('/doctors-by-poli/{poli_id}', [PasienDashboardController::class, 'getDoctorsByPoli'])->name('doctors.by.poli');
    });

    // --- GRUP ROUTE UNTUK ADMIN ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Menggunakan AdminDashboardController yang sudah di-import di atas
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Tempatkan rute admin lainnya di sini jika ada
    });

    // Anda bisa menambahkan grup rute untuk role lain di sini (dokter, apotek, dll.)

});

