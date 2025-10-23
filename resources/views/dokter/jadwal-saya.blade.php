@extends('layouts.dokter_layout')

@section('title', 'Jadwal Praktik Saya')

@section('content')
<div class="w-full max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 text-center">Jadwal Praktik Saya</h2>
    </div>

    {{-- Tampilkan tabel HANYA jika dokter memiliki jadwal --}}
    @if ($hasAnySchedule)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-emerald-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-100 uppercase tracking-wider">
                                Hari
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-100 uppercase tracking-wider">
                                Waktu Praktik
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-100 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        {{-- Loop hanya pada hari yang ada jadwalnya --}}
                        @foreach ($daysOrder as $day)
                            @if(isset($groupedSchedules[$day]) && $groupedSchedules[$day]->isNotEmpty())
                                @foreach ($groupedSchedules[$day] as $index => $schedule)
                                    <tr>
                                        {{-- Tampilkan nama hari hanya di baris pertama untuk hari tersebut --}}
                                        @if ($index == 0)
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 align-top" rowspan="{{ $groupedSchedules[$day]->count() }}">
                                                {{ $day }}
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $schedule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    {{-- Tampilkan pesan jika tidak ada jadwal sama sekali dalam seminggu --}}
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
             <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
             </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak Ada Jadwal Praktik</h3>
            <p class="mt-1 text-sm text-gray-500">Maaf, tidak ada jadwal praktik yang terdaftar untuk Anda minggu ini. Selamat beristirahat.</p>
        </div>
    @endif
</div>
@endsection

