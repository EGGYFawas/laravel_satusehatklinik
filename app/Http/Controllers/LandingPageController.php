<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Article;
use App\Models\LandingPageContent; // <--- 1. TAMBAHAN PENTING: Import Model Baru
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
        // === BAGIAN 1: DATA DOKTER (Existing) ===
        $doctorsWithSchedules = Doctor::has('doctorSchedules')
                                    ->with('user', 'doctorSchedules')
                                    ->get();

        // === BAGIAN 2: DATA ARTIKEL (Existing) ===
        $articles = Article::whereNotNull('published_at')
                            ->where('published_at', '<=', now())
                            ->latest('published_at')
                            ->limit(3)
                            ->get();

        // === BAGIAN 3: DATA KONTEN DINAMIS (BARU) ===
        // Mengambil semua settingan teks/gambar dari tabel landing_page_contents
        $rawContents = LandingPageContent::all();
        
        // Mengubah format menjadi array Key => Value
        // Contoh: ['hero_title' => 'Klinik Sehat', 'hero_image' => 'img/foto.jpg']
        // Tujuannya agar mudah dipanggil di Blade view.
        $content = $rawContents->pluck('value', 'key')->toArray();

        // === BAGIAN 4: KIRIM SEMUA KE VIEW ===
        return view('landing', [
            'doctors'  => $doctorsWithSchedules,
            'articles' => $articles,
            'content'  => $content, // <--- Data konten dinamis dikirim di sini
        ]);
    }
}