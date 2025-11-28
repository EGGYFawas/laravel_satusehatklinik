<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Article; // <--- 1. TAMBAHKAN IMPORT INI
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    /**
     * Menampilkan halaman landing page.
     */
    public function index(): View
    {
        // Kode untuk mengambil dokter (ini sudah Anda miliki)
        $doctorsWithSchedules = Doctor::has('doctorSchedules')
                                    ->with('user', 'doctorSchedules')
                                    ->get();

        // 2. TAMBAHKAN KODE INI UNTUK MENGAMBIL ARTIKEL
        $articles = Article::whereNotNull('published_at')
                            ->where('published_at', '<=', now())
                            ->latest('published_at')
                            ->limit(3)
                            ->get();

        // 3. PERBARUI 'return view' UNTUK MENGIRIM KEDUA DATA
        return view('landing', [
            'doctors' => $doctorsWithSchedules,
            'articles' => $articles, // <-- Kirim data artikel
        ]);
    }
}