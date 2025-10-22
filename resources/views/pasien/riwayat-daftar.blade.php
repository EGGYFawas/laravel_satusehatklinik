@extends('layouts.pasien_layout')

@section('title', 'Riwayat Kunjungan')

@push('styles')
<style>
    /* Tambahkan style kustom jika diperlukan */
</style>
@endpush

@section('content')
<div class="w-full max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <h2 class="text-2xl font-bold text-gray-800 border-b pb-4 mb-6">Riwayat Kunjungan</h2>

        <p class="text-gray-600 mb-6">Berikut adalah daftar pasien yang terhubung dengan akun Anda. Pilih nama untuk melihat detail riwayat kunjungan.</p>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Pasien
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hubungan
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kunjungan Terakhir
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($patientHistories as $history)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $history->full_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $history->relationship }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if ($history->last_visit_date)
                                    {{-- Format tanggal menggunakan Carbon dari properti $casts --}}
                                    {{ $history->last_visit_date->translatedFormat('l, d F Y') }}
                                @else
                                    Belum ada riwayat
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('pasien.riwayat.show', $history->patient_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#24306E] hover:bg-[#1a224d] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#24306E]">
                                    Lihat Detail Riwayat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                Belum ada riwayat kunjungan untuk Anda atau anggota keluarga yang pernah Anda daftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
