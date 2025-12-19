@extends('layouts.pasien_layout')

@section('title', $article->title)

@push('styles')
<style>
    .article-body h1 { font-size: 1.875rem; font-weight: 700; margin: 1.5em 0 0.5em; }
    .article-body h2 { font-size: 1.5rem; font-weight: 700; margin: 1.5em 0 0.5em; }
    .article-body h3 { font-size: 1.25rem; font-weight: 600; margin: 1.5em 0 0.5em; }
    .article-body p { margin-bottom: 1em; line-height: 1.6; color: #374151; }
    .article-body ul { list-style: disc; margin-left: 1.5rem; margin-bottom: 1em; }
    .article-body ol { list-style: decimal; margin-left: 1.5rem; margin-bottom: 1em; }
    
    /* [MODIFIKASI] Style untuk Gambar di dalam konten */
    .article-body img { 
        max-width: 100%;      /* Agar tidak melebar melebihi container */
        max-height: 500px;    /* Batasi tinggi maksimal agar tidak terlalu panjang ke bawah */
        width: auto;          /* Biarkan lebar menyesuaikan proporsi */
        height: auto;         /* Biarkan tinggi menyesuaikan proporsi */
        display: block;       /* Agar margin auto bekerja (tengah) */
        margin: 1.5em auto;   /* Posisi gambar di tengah (center) */
        border-radius: 0.5rem; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); /* Sedikit bayangan agar elegan */
    }

    .article-body a { color: #24306E; text-decoration: underline; }
    .article-body blockquote { border-left: 4px solid #24306E; padding-left: 1rem; margin: 1.5em 0; font-style: italic; color: #4b5563; background: #f3f4f6; padding: 1rem; }
</style>
@endpush

@section('content')
<div class="w-full max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        
        {{-- [MODIFIKASI] Gambar Sampul --}}
        {{-- Ukuran tinggi diubah menjadi h-56 (mobile) dan md:h-80 (desktop) agar lebih proporsional --}}
        <div class="w-full bg-gray-100 flex justify-center items-center overflow-hidden">
            <img src="{{ $article->image_url ? asset('storage/' . $article->image_url) : 'https://placehold.co/1200x600/E9E6E6/24306E?text=Artikel' }}" 
                 alt="{{ $article->title }}" 
                 class="w-full h-56 md:h-80 object-cover object-center">
        </div>

        <div class="p-6 md:p-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">{{ $article->title }}</h1>

            <div class="text-sm text-gray-500 mb-8 pb-4 border-b flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ $article->published_at ? $article->published_at->translatedFormat('l, d F Y') : 'N/A' }}
                @if($article->author)
                    <span class="mx-2">•</span>
                    Oleh {{ $article->author->name }}
                @endif
            </div>

            <div class="article-body text-gray-700 leading-relaxed">
                {!! $article->content !!}
            </div>

            <div class="mt-10 pt-6 border-t">
                <a href="{{ route('pasien.artikel.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-lg transition">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                     </svg>
                    Kembali ke Daftar Artikel
                </a>
            </div>
        </div>
    </div>
</div>
@endsection