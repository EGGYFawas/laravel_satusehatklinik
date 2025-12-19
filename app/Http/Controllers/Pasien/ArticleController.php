<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Carbon\Carbon;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('search');

        $articles = Article::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now())
            ->when($searchQuery, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('content', 'like', '%' . $search . '%');
                });
            })
            ->latest('published_at')
            ->paginate(9);

        // [UBAH DISINI] Sesuai struktur folder: views/pasien/artikel/index.blade.php
        return view('pasien.artikel.index', compact('articles', 'searchQuery'));
    }

    public function show(Article $article)
    {
        if (!$article->published_at || $article->published_at > Carbon::now()) {
            abort(404);
        }

        // [UBAH DISINI] Sesuai struktur folder: views/pasien/artikel/show.blade.php
        return view('pasien.artikel.show', compact('article'));
    }
}