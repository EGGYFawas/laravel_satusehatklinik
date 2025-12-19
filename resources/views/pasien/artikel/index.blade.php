@extends('layouts.pasien_layout')

@section('title', 'Artikel Kesehatan')

@push('styles')
<style>
    .article-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .article-content {
        flex-grow: 1;
    }
</style>
@endpush

@section('content')
<div class="w-full max-w-6xl mx-auto">
    {{-- Container Judul --}}
    <div class="bg-white rounded-xl shadow-lg p-4 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 text-center">Artikel Kesehatan</h2>
    </div>

    {{-- Form Pencarian --}}
    <div class="mb-8">
        <form action="{{ route('pasien.artikel.index') }}" method="GET">
            <div class="relative">
                <input
                    type="text"
                    name="search"
                    placeholder="Cari judul atau isi artikel..."
                    value="{{ $searchQuery ?? '' }}"
                    class="w-full p-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:ring-[#24306E] focus:border-[#24306E]"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                
                @if($searchQuery)
                 <a href="{{ route('pasien.artikel.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm text-gray-500 hover:text-gray-700" title="Reset Pencarian">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                     </svg>
                 </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Grid Artikel --}}
    @if($articles->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($articles as $article)
                <a href="{{ route('pasien.artikel.show', $article->slug) }}" class="article-card bg-white rounded-xl shadow-lg overflow-hidden flex flex-col no-underline hover:no-underline group">
                    
                    {{-- [FIX] Logic Gambar --}}
                    <div class="overflow-hidden h-48">
                         <img src="{{ $article->image_url ? asset('storage/' . $article->image_url) : 'https://placehold.co/600x400/E9E6E6/24306E?text=Artikel' }}" 
                              alt="{{ $article->title }}" 
                              class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                    </div>
                    
                    <div class="p-6 article-content flex flex-col">
                        <h3 class="font-bold text-lg mb-2 text-gray-800 line-clamp-2 group-hover:text-[#24306E] transition">{{ $article->title }}</h3>
                        <p class="text-gray-600 text-sm flex-grow line-clamp-3">
                            {{ Str::limit(strip_tags($article->content), 120) }}
                        </p>
                        <div class="mt-4 text-xs text-gray-500">
                             Dipublikasikan pada {{ $article->published_at ? $article->published_at->translatedFormat('d F Y') : 'N/A' }}
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 text-right border-t border-gray-100">
                        <span class="text-sm text-[#24306E] font-semibold hover:underline">Baca Selengkapnya &rarr;</span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Navigasi Paginasi --}}
        <div class="mt-12">
            {{ $articles->appends(['search' => $searchQuery])->links() }}
        </div>

    @else
        <div class="text-center text-gray-500 py-16 bg-white rounded-xl shadow-lg border border-dashed border-gray-200">
             <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6M7 8h6" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">
                @if($searchQuery) Artikel Tidak Ditemukan @else Belum Ada Artikel @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                @if($searchQuery) Tidak ada artikel yang cocok dengan pencarian "{{ $searchQuery }}". @else Belum ada artikel kesehatan yang dipublikasikan. @endif
            </p>
        </div>
    @endif
</div>
@endsection