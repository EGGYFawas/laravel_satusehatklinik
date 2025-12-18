<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Article;
use App\Models\LandingPageContent; // Import Model
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

        // === BAGIAN 3: DATA KONTEN DINAMIS (UPDATE LOGIKA BARU) ===
        // Mengambil semua settingan teks/gambar
        $rawContents = LandingPageContent::all();
        
        // Logika Mapping Cerdas:
        // Cek tipe datanya. 
        // - Jika 'image' -> Ambil dari kolom 'image' (yang baru kita buat di migrasi)
        // - Jika 'text'/'textarea' -> Ambil dari kolom 'value'
        $content = $rawContents->mapWithKeys(function ($item) {
            $isi = $item->type === 'image' ? $item->image : $item->value;
            return [$item->key => $isi];
        })->toArray();

        // === BAGIAN 4: KIRIM SEMUA KE VIEW ===
        return view('landing', [
            'doctors'  => $doctorsWithSchedules,
            'articles' => $articles,
            'content'  => $content, 
        ]);
    }
}