@extends('layouts.pasien_layout')

@section('title', 'Jadwal Dokter')

@push('styles')
<style>
    /* Styling tambahan jika diperlukan */
    .schedule-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .schedule-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@section('content')
<div class="w-full max-w-6xl mx-auto">
    {{-- Container Judul Utama --}}
    <div class="bg-white rounded-xl shadow-lg p-4 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 text-center">Jadwal Praktik Dokter</h2>
    </div>

    {{-- [PENAMBAHAN BARU] Card Jadwal Hari Ini --}}
    <div class="bg-gradient-to-r from-blue-950 to-blue-300 rounded-xl shadow-lg p-6 mb-8 border border-cyan-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
            <div>
                <h3 class="text-xl font-bold text-white">Jadwal Hari Ini</h3>
                <p class="text-white">{{ $now->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="mt-2 sm:mt-0 text-lg font-semibold text-gray-700 bg-white/50 px-3 py-1 rounded-lg shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                {{ $now->format('H:i') }} WIB
            </div>
        </div>

        @if($todaySchedules->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($todaySchedules as $schedule)
                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
                        <p class="font-semibold text-base text-gray-800">{{ $schedule->doctor->user->full_name ?? 'N/A' }}</p>
                        <p class="text-xs font-medium text-emerald-700">{{ $schedule->doctor->poli->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 italic py-4">Tidak ada dokter yang praktik hari ini.</p>
        @endif
    </div>
    {{-- [/PENAMBAHAN BARU] --}}


    {{-- Tampilan Jadwal Mingguan (Tidak Diubah) --}}
    <div class="space-y-8">
        @foreach ($daysOrder as $day)
            @if(isset($groupedSchedules[$day]) && $groupedSchedules[$day]->isNotEmpty())
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <h3 class="text-xl font-bold text-gray-100 p-4 bg-[#24306E]">
                        {{ $day }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        @foreach ($groupedSchedules[$day] as $schedule)
                            <div class="schedule-card border border-gray-200 rounded-lg p-4 bg-gray-50 flex flex-col justify-between" data-poli-id="{{ $schedule->doctor->poli->id }}">
                                <div>
                                    <p class="font-bold text-lg text-[#1a224d]">{{ $schedule->doctor->user->full_name ?? 'Nama Dokter Tidak Tersedia' }}</p>
                                    <p class="text-sm font-semibold text-emerald-700">{{ $schedule->doctor->poli->name ?? 'Poli Tidak Tersedia' }}</p>
                                    <p class="text-gray-600 mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        {{-- @if($groupedSchedules->isEmpty()) --}}
        {{-- Logika ini mungkin tidak relevan lagi jika minimal ada jadwal di hari lain --}}
        {{-- <div class="text-center text-gray-500 py-16 bg-white rounded-xl shadow-lg">
             <p>Jadwal dokter tidak tersedia saat ini.</p>
           </div> --}}
        {{-- @endif --}}
    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk filter (jika diaktifkan) --}}
{{-- <script>
    document.getElementById('poliFilter')?.addEventListener('change', function() {
        const selectedPoliId = this.value;
        const scheduleCards = document.querySelectorAll('.schedule-card');

        scheduleCards.forEach(card => {
            const cardPoliId = card.getAttribute('data-poli-id');
            if (selectedPoliId === 'all' || cardPoliId === selectedPoliId) {
                card.style.display = 'flex'; // Tampilkan kartu
            } else {
                card.style.display = 'none'; // Sembunyikan kartu
            }
        });
    });
</script> --}}
@endpush

