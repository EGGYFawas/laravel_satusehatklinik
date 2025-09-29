<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Panggil helper untuk memuat konfigurasi dari database
        
        // NONAKTIFKAN SEMENTARA BAGIAN INI UNTUK MIGRASI
        // if (class_exists(\App\Models\Satusehat::class)) {
        //     loadSatusehatConfig();
        // }
    }
}