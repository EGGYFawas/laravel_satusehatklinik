@extends('layouts.admin_layout')

@section('content')

    <div class="p-8 rounded-lg text-white mb-8 bg-gradient-to-r from-[#4F46E5] to-[#2b4b9b]">
        <h2 class="text-3xl font-bold">Welcome back, Admin!</h2>
        <p class="text-indigo-200 mt-1">Here's what's happening with your system today.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-1a6 6 0 00-5.176-5.97m8.176 5.97v-1a6 6 0 00-9-5.197m0-3.466a4 4 0 01-4.472-4.472A4 4 0 017 3c1.112 0 2.11.442 2.83 1.171A4 4 0 0113 7a4 4 0 01-4.472 4.472z" /></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-slate-500">Total Dokter</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalDokter ?? 0 }}</p>
            </div>
        </div>
         <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-1a6 6 0 00-5.176-5.97m8.176 5.97v-1a6 6 0 00-9-5.197m0-3.466a4 4 0 01-4.472-4.472A4 4 0 017 3c1.112 0 2.11.442 2.83 1.171A4 4 0 0113 7a4 4 0 01-4.472 4.472z" /></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-slate-500">Total Pasien</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalPasien ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-purple-100 p-3 rounded-full">
                <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-1a6 6 0 00-5.176-5.97m8.176 5.97v-1a6 6 0 00-9-5.197m0-3.466a4 4 0 01-4.472-4.472A4 4 0 017 3c1.112 0 2.11.442 2.83 1.171A4 4 0 0113 7a4 4 0 01-4.472 4.472z" /></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-slate-500">Total Petugas Loket</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalPetugas ?? 0 }}</p>
            </div>
        </div>
         <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-amber-100 p-3 rounded-full">
                <svg class="h-6 w-6 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div class="ml-4">
                <p class="text-sm text-slate-500">Kunjungan Hari Ini</p>
                <p class="text-2xl font-bold text-slate-800">{{ $kunjunganHariIni ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-4">
                <a href="#" class="flex items-center justify-between p-4 rounded-lg bg-blue-50 hover:bg-blue-100 transition">
                    <div class="flex items-center">
                        <div class="bg-blue-200 p-2 rounded-lg mr-4">
                            <svg class="h-6 w-6 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="font-semibold">Tambah Pasien Baru</p>
                            <p class="text-sm text-slate-500">Daftarkan pasien baru ke sistem.</p>
                        </div>
                    </div>
                     <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a href="#" class="flex items-center justify-between p-4 rounded-lg bg-green-50 hover:bg-green-100 transition">
                     <div class="flex items-center">
                        <div class="bg-green-200 p-2 rounded-lg mr-4">
                            <svg class="h-6 w-6 text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <p class="font-semibold">Buat Jadwal Kunjungan</p>
                            <p class="text-sm text-slate-500">Atur jadwal pertemuan pasien & dokter.</p>
                        </div>
                    </div>
                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Aktivitas Pasien Terbaru</h3>
                <a href="#" class="text-sm font-medium text-indigo-600 hover:underline">View all</a>
            </div>
            <div class="space-y-4">
                @forelse ($aktivitasTerbaru as $aktivitas)
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $aktivitas['avatar'] }}" alt="Avatar">
                        <div class="ml-4 flex-1">
                            <p class="font-semibold">{{ $aktivitas['nama'] }}</p>
                            <p class="text-sm text-slate-500">{{ $aktivitas['deskripsi'] }}</p>
                        </div>
                        <div class="text-right">
                             <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $aktivitas['status_bg'] }} {{ $aktivitas['status_color'] }}">{{ $aktivitas['status_text'] }}</span>
                             <p class="text-xs text-slate-400 mt-1">{{ $aktivitas['waktu'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-4">Belum ada aktivitas terbaru.</p>
                @endforelse
            </div>
        </div>
    </div>

@endsection