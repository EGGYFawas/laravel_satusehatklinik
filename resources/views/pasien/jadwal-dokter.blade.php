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
    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Jadwal Praktik Dokter</h2>

    {{-- Filter berdasarkan Poli (Opsional) --}}
    {{-- <div class="mb-6 flex justify-center">
        <select id="poliFilter" class="p-2 border border-gray-300 rounded-md shadow-sm w-full max-w-xs">
            <option value="all">Semua Poli</option>
            @foreach ($polis as $poli)
                <option value="{{ $poli->id }}">{{ $poli->name }}</option>
            @endforeach
        </select>
    </div> --}}

    <div class="space-y-8">
        {{-- Loop berdasarkan hari yang sudah diurutkan --}}
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
                                {{-- Tombol daftar bisa ditambahkan di sini jika ingin integrasi langsung --}}
                                {{-- <a href="#" class="mt-4 text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">Daftar ke Dokter Ini</a> --}}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        @if($groupedSchedules->isEmpty())
             <div class="text-center text-gray-500 py-16 bg-white rounded-xl shadow-lg">
                 <p>Jadwal dokter tidak tersedia saat ini.</p>
             </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk filter (jika diaktifkan) --}}
<script>
    document.getElementById('poliFilter').addEventListener('change', function() {
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
</script>
@endpush
