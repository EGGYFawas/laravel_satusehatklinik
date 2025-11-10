<?php

// [MODIFIKASI] Namespace diubah ke root Controllers
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article; // Pastikan model Article sudah ada
use Carbon\Carbon;

// [MODIFIKASI] Nama class (tanpa Pasien)
class ArticleController extends Controller
{
    /**
     * Menampilkan daftar artikel kesehatan dengan fitur pencarian.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->input('search');

        $articles = Article::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now())
            ->when($searchQuery, function ($query, $search) {
                return $query->where('title', 'like', '%' . $search . '%');
            })
            ->latest('published_at')
            ->paginate(9); 

        // [MODIFIKASI] Menggunakan view publik 'artikel-index'
        return view('artikel-index', compact('articles', 'searchQuery'));
    }

    /**
     * Menampilkan detail satu artikel kesehatan.
     */
    public function show(Article $article)
    {
        if (!$article->published_at || $article->published_at > Carbon::now()) {
            abort(404);
        }

        // [MODIFIKASI] Menggunakan view publik 'artikel-show'
        return view('artikel-show', compact('article'));
    }
}