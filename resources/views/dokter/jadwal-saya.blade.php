@extends('layouts.dokter_layout')

@section('title', 'Jadwal Praktik Saya')

@section('content')
<div class="w-full max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Jadwal Praktik Saya</h2>
    
    {{-- Tombol untuk Edit Jadwal (jika nanti ada fiturnya) --}}
    {{-- <div class="text-right mb-6">
        <a href="#" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg">
            Atur Jadwal Saya
        </a>
    </div> --}}

    <div class="space-y-6">
        @foreach ($daysOrder as $day)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <h3 class="text-xl font-bold text-gray-100 p-4 bg-emerald-700">
                    {{ $day }}
                </h3>
                <div class="p-6">
                    @if(isset($groupedSchedules[$day]) && $groupedSchedules[$day]->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($groupedSchedules[$day] as $schedule)
                                <li class="flex items-center justify-between p-3 bg-emerald-50 rounded-md border border-emerald-200">
                                    <p class="text-gray-800 font-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB
                                    </p>
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $schedule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-gray-500 italic">Tidak ada jadwal praktik pada hari ini.</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
