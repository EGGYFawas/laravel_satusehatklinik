<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin.
     */
    public function index()
    {
        // Data ini idealnya diambil dari database
        $totalDokter = 15;
        $totalPasien = 352;
        $totalPetugas = 5;
        $kunjunganHariIni = 42;

        $aktivitasTerbaru = [
            [
                'avatar' => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=random',
                'nama' => 'Budi Santoso',
                'deskripsi' => 'Mendaftar untuk konsultasi dengan Dr. Amanda',
                'waktu' => '10 minutes ago',
                'status_bg' => 'bg-green-100',
                'status_color' => 'text-green-700',
                'status_text' => 'Baru'
            ],
            [
                'avatar' => 'https://ui-avatars.com/api/?name=Citra+Wijaya&background=random',
                'nama' => 'Citra Wijaya',
                'deskripsi' => 'Selesai melakukan pembayaran administrasi',
                'waktu' => '35 minutes ago',
                'status_bg' => 'bg-blue-100',
                'status_color' => 'text-blue-700',
                'status_text' => 'Selesai'
            ],
             [
                'avatar' => 'https://ui-avatars.com/api/?name=Eka+Pratama&background=random',
                'nama' => 'Eka Pratama',
                'deskripsi' => 'Membatalkan janji temu',
                'waktu' => '1 hour ago',
                'status_bg' => 'bg-red-100',
                'status_color' => 'text-red-700',
                'status_text' => 'Dibatalkan'
            ],
        ];


        // Mengirim data ke view
        return view('admin.dashboard', compact(
            'totalDokter',
            'totalPasien',
            'totalPetugas',
            'kunjunganHariIni',
            'aktivitasTerbaru'
        ));
    }
}