<?php

namespace App\Http; // Pastikan namespace benar

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... (bagian middleware dan middlewareGroups biarkan sama) ...

    /**
     * The application's route middleware aliases.
     *
     * These aliases may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // --- HAPUS ALIAS KUSTOM ANDA ---
        // 'role' => \App\Http\Middleware\CheckRole::class, // HAPUS BARIS INI

        // Spatie secara otomatis menambahkan alias 'role' dan 'permission'.
        // Tidak perlu menambahkan apa-apa lagi.
    ];
}

