@extends('layouts.guest') {{-- [MODIFIKASI] Menggunakan layout guest --}}

@section('title', $article->title)

@push('styles')
<style>
    /* Styling untuk konten artikel (tidak berubah) */
    .article-body h1, .article-body h2, .article-body h3 { font-weight: 700; margin-top: 1.5em; margin-bottom: 0.5em; line-height: 1.3; }
    .article-body h1 { font-size: 1.875rem; }
    .article-body h2 { font-size: 1.5rem; }
    .article-body h3 { font-size: 1.25rem; }
    .article-body p { margin-bottom: 1em; line-height: 1.6; }
    .article-body ul, .article-body ol { margin-left: 1.5rem; margin-bottom: 1em; }
    .article-body ul { list-style: disc; }
    .article-body ol { list-style: decimal; }
    .article-body li { margin-bottom: 0.5em; }
    .article-body a { color: #24306E; text-decoration: underline; }
    .article-body a:hover { color: #1a224d; }
    .article-body img { max-width: 100%; height: auto; margin-top: 1em; margin-bottom: 1em; border-radius: 0.5rem; }
    .article-body strong { font-weight: 600; }
    .article-body em { font-style: italic; }
    .article-body blockquote { border-left: 4px solid #ccc; padding-left: 1rem; margin-left: 0; margin-right: 0; margin-bottom: 1em; font-style: italic; color: #666; }
</style>
@endpush

@section('content')
{{-- [MODIFIKASI] Tambahkan class 'bg-white' dan 'py-16' untuk section --}}
<section class="bg-white py-16">
<div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border">
        <img src="{{ $article->image_url ?? 'https://placehold.co/1200x600/E9E6E6/24306E?text=Artikel' }}" alt="Gambar Artikel: {{ $article->title }}" class="w-full h-64 md:h-96 object-cover">

        <div class="p-6 md:p-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">{{ $article->title }}</h1>

            <div class="text-sm text-gray-500 mb-8 pb-4 border-b">
                Dipublikasikan pada {{ $article->published_at ? $article->published_at->translatedFormat('l, d F Y') : 'N/A' }}
            </div>

            <div class="article-body text-gray-700 leading-relaxed">
                {!! $article->content !!}
            </div>

            <div class="mt-10 pt-6 border-t">
                <a href="{{ route('artikel.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md"> {{-- [MODIFIKASI] Route publik --}}
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                     </svg>
                    Kembali ke Daftar Artikel
                </a>
            </div>
        </div>
    </div>
</div>
</section>
@endsection