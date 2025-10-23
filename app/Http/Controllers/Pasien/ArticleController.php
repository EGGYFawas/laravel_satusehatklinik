<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article; // Pastikan model Article sudah ada
use Carbon\Carbon;

class ArticleController extends Controller
{
    /**
     * Menampilkan daftar artikel kesehatan dengan fitur pencarian.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->input('search');

        $articles = Article::query()
            // Hanya tampilkan artikel yang sudah dipublikasi
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now())
            // Terapkan filter pencarian jika ada
            ->when($searchQuery, function ($query, $search) {
                return $query->where('title', 'like', '%' . $search . '%');
                    // ->orWhere('content', 'like', '%' . $search . '%'); // Uncomment jika ingin mencari di konten juga
            })
            // Urutkan berdasarkan tanggal publikasi terbaru
            ->latest('published_at')
            // Paginasi hasil
            ->paginate(9); // Tampilkan 9 artikel per halaman

        return view('pasien.artikel-index', compact('articles', 'searchQuery'));
    }

    /**
     * Menampilkan detail satu artikel kesehatan.
     * Menggunakan Route Model Binding dengan slug.
     */
    public function show(Article $article)
    {
        // Pastikan artikel yang diakses sudah dipublikasi
        if (!$article->published_at || $article->published_at > Carbon::now()) {
            abort(404); // Tampilkan halaman tidak ditemukan jika belum publish
        }

        return view('pasien.artikel-show', compact('article'));
    }
}
