@extends('layouts.guest')

@section('title', $article->title)

@push('styles')
<style>
    .article-body h1 { font-size: 1.875rem; font-weight: 700; margin: 1.5em 0 0.5em; color: #1f2937; }
    .article-body h2 { font-size: 1.5rem; font-weight: 700; margin: 1.5em 0 0.5em; color: #374151; }
    .article-body p { margin-bottom: 1em; line-height: 1.7; color: #4b5563; }
    .article-body ul { list-style: disc; margin-left: 1.5rem; margin-bottom: 1em; }
    .article-body img { max-width: 100%; height: auto; margin: 1.5em 0; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .article-body a { color: #24306E; text-decoration: underline; }
    .article-body blockquote { border-left: 4px solid #24306E; padding-left: 1rem; font-style: italic; background: #f9fafb; padding: 1rem; border-radius: 0 0.5rem 0.5rem 0; }
</style>
@endpush

@section('content')
<section class="bg-gray-50 py-12">
<div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
        
        {{-- Gambar Sampul Utama --}}
        <img src="{{ $article->image_url ? asset('storage/' . $article->image_url) : 'https://placehold.co/1200x600/E9E6E6/24306E?text=Artikel+Kesehatan' }}" 
             alt="{{ $article->title }}" 
             class="w-full h-64 md:h-[400px] object-cover">

        <div class="p-6 md:p-12">
            {{-- Header --}}
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight">{{ $article->title }}</h1>
            
            <div class="flex items-center text-sm text-gray-500 mb-8 pb-6 border-b border-gray-100">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Dipublikasikan pada {{ $article->published_at ? $article->published_at->translatedFormat('l, d F Y') : 'N/A' }}
                @if($article->author)
                    <span class="mx-2">•</span>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    {{ $article->author->name }}
                @endif
            </div>
            
            {{-- Isi Artikel --}}
            <div class="article-body">
                {!! $article->content !!}
            </div>
            
            {{-- Footer Navigasi --}}
            <div class="mt-12 pt-8 border-t border-gray-100">
                <a href="{{ route('artikel.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg transition duration-200">
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