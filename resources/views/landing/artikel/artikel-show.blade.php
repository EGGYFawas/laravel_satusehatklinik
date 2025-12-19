@extends('layouts.guest')

@section('title', $article->title)

@push('styles')
<style>
    /* Styling Typography Artikel - Disamakan dengan tampilan Pasien */
    .article-body h1 { font-size: 1.875rem; font-weight: 700; margin: 1.5em 0 0.5em; color: #1f2937; }
    .article-body h2 { font-size: 1.5rem; font-weight: 700; margin: 1.5em 0 0.5em; color: #374151; }
    .article-body h3 { font-size: 1.25rem; font-weight: 600; margin: 1.5em 0 0.5em; color: #4b5563; }
    
    .article-body p { 
        margin-bottom: 1.2em; 
        line-height: 1.8; 
        color: #374151; 
        font-size: 1.05rem;
    }
    
    .article-body ul { list-style: disc; margin-left: 1.5rem; margin-bottom: 1em; color: #374151; }
    .article-body ol { list-style: decimal; margin-left: 1.5rem; margin-bottom: 1em; color: #374151; }
    
    /* [PENTING] Kontrol Gambar Konten agar tidak terlalu besar */
    .article-body img { 
        display: block;           
        max-width: 85%;           /* Lebar maksimal 85% dari container */
        max-height: 500px;        /* Tinggi maksimal 500px */
        width: auto;              
        height: auto; 
        margin: 2.5em auto;       /* Posisi Tengah (Center) */
        border-radius: 0.75rem;   
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
    }
    
    .article-body a { color: #24306E; text-decoration: underline; font-weight: 500; }
    .article-body blockquote { 
        border-left: 4px solid #24306E; 
        padding-left: 1rem; 
        margin: 1.5em 0; 
        font-style: italic; 
        color: #4b5563; 
        background: #f8fafc; 
        padding: 1.5rem; 
        border-radius: 0 0.5rem 0.5rem 0;
    }
</style>
@endpush

@section('content')
<div class="w-full max-w-4xl mx-auto pb-12 pt-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        
        {{-- Gambar Sampul Utama --}}
        <div class="relative h-64 md:h-[450px] w-full bg-gray-100">
            <img src="{{ $article->image_url ? asset('storage/' . $article->image_url) : 'https://placehold.co/1200x600/E9E6E6/24306E?text=Artikel' }}" 
                 alt="{{ $article->title }}" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        </div>

        <div class="p-6 md:p-12 relative">
            {{-- Judul Artikel --}}
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight">{{ $article->title }}</h1>

            {{-- Info Meta --}}
            <div class="flex flex-wrap items-center text-sm text-gray-500 mb-10 pb-6 border-b border-gray-100 gap-y-2">
                <div class="flex items-center mr-6">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ $article->published_at ? $article->published_at->translatedFormat('l, d F Y') : 'N/A' }}
                </div>
                @if($article->author)
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="font-medium text-gray-700">{{ $article->author->name ?? $article->author->full_name }}</span>
                </div>
                @endif
            </div>

            {{-- Isi Konten --}}
            <div class="article-body text-gray-700 leading-relaxed">
                {!! $article->content !!}
            </div>

            {{-- Footer Navigasi --}}
            <div class="mt-12 pt-8 border-t border-gray-100">
                <a href="{{ route('artikel.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg transition-colors border border-gray-200">
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