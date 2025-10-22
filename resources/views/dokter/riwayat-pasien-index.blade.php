@extends('layouts.dokter_layout')

@section('title', 'Riwayat Pasien')

@section('content')
<div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
    <h2 class="text-2xl font-bold text-gray-800 border-b pb-4 mb-6">Cari Riwayat Pasien</h2>

    {{-- Form Pencarian --}}
    <form method="GET" action="{{ route('dokter.riwayat-pasien.index') }}" class="mb-6">
        <div class="flex items-center">
            <input type="text" name="search" value="{{ $searchQuery ?? '' }}" placeholder="Cari berdasarkan Nama atau NIK..." class="flex-grow p-2 border border-gray-300 rounded-l-md focus:ring-emerald-500 focus:border-emerald-500">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold p-2 rounded-r-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>
    </form>

    {{-- Hasil Pencarian / Daftar Pasien --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Kunjungan</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($patients as $patient)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $patient->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $patient->nik ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $patient->clinic_queues_count }} kali</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('dokter.riwayat-pasien.show', $patient->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                            @if($searchQuery)
                                Pasien dengan nama atau NIK "{{ $searchQuery }}" tidak ditemukan.
                            @else
                                Belum ada riwayat pasien yang tercatat untuk Anda.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-6">
        {{ $patients->appends(['search' => $searchQuery])->links() }}
    </div>

</div>
@endsection
