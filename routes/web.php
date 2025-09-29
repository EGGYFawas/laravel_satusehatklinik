<?php

use Illuminate\Support\Facades\Route;

// Import untuk Controller Aplikasi
use App\Http\Controllers\AppInfoController;
// ... (sisa use statement Anda yang lain)
use App\Http\Controllers\BundleController;

// Import untuk Controller Autentikasi & Redirect
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardRedirectController;

// BENAR: Tambahkan 'use' statement untuk AdminDashboardController Anda
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;


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
    
    // Rute "Pintu Gerbang" setelah login
    Route::get('/dashboard', [DashboardRedirectController::class, 'index'])->name('dashboard');

    // Rute Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // --- GRUP ROUTE UNTUK PASIEN ---
    Route::middleware(['role:pasien'])->prefix('pasien')->name('pasien.')->group(function () {
        Route::get('/dashboard', function () {
            return view('pasien.dashboard'); 
        })->name('dashboard');
    });

    // --- GRUP ROUTE UNTUK ADMIN (SATU-SATUNYA YANG BENAR) ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Menggunakan AdminDashboardController yang sudah di-import di atas
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Tempatkan rute admin lainnya di sini jika ada
        // Route::resource('pasien', App\Http\Controllers\PasienController::class);
    });

    // HAPUS BLOK ADMIN YANG DUPLIKAT DARI SINI
});