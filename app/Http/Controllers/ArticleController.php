<?php

namespace App\Http\Controllers;

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

        // [PERUBAHAN PENTING ADA DISINI]
        // Pastikan mengarah ke 'landing.artikel.index'
        return view('landing.artikel.artikel-index', compact('articles', 'searchQuery'));
    }

    public function show(Article $article)
    {
        if (!$article->published_at || $article->published_at > Carbon::now()) {
            abort(404);
        }

        // [PERUBAHAN PENTING ADA DISINI]
        // Pastikan mengarah ke 'landing.artikel.show'
        return view('landing.artikel.artikel-show', compact('article'));
    }
}