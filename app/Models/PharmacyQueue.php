<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyQueue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinic_queue_id',
        'prescription_id',
        'pharmacy_queue_number',
        'status',
        'entry_time',
        'start_racik_time',
        'finish_racik_time',
        'taken_time',
        'user_id', // [PENTING] Pastikan kolom ini ada di database untuk menyimpan siapa petugasnya
    ];

    /**
     * Mendapatkan data antrian klinik asal antrian farmasi ini.
     */
    public function clinicQueue()
    {
        return $this->belongsTo(ClinicQueue::class);
    }

    /**
     * Mendapatkan data resep yang terkait dengan antrian farmasi ini.
     */
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    /**
     * [BARU] Relasi ke User (Petugas/Apoteker).
     * Ini yang dipanggil di InvoiceController: ->with('pharmacyQueue.pharmacist')
     */
    public function pharmacist()
    {
        // Asumsi: Foreign Key di tabel pharmacy_queues adalah 'user_id'
        // Jika di database namanya 'pharmacist_id', ubah 'user_id' jadi 'pharmacist_id'
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * [PERBAIKAN] Menambahkan method statis yang hilang untuk membuat nomor antrean.
     *
     * Logika ini akan mencari nomor antrean apotek terbesar yang dibuat HARI INI,
     * lalu menambahkannya dengan 1. Jika belum ada, akan dimulai dari 1.
     */
    public static function generateQueueNumber(): int
    {
        // 1. Dapatkan awal hari ini sesuai timezone aplikasi Anda
        $today = now(config('app.timezone'))->startOfDay();

        // 2. Cari nomor antrean TERBESAR yang dibuat HARI INI (berdasarkan created_at)
        //    Jika Anda menggunakan 'entry_time' untuk pelacakan, Anda juga bisa memakai:
        //    $lastQueueNumber = self::where('entry_time', '>=', $today)->max('pharmacy_queue_number');
        
        $lastQueueNumber = self::where('created_at', '>=', $today)
                               ->max('pharmacy_queue_number');

        // 3. Tambahkan 1 ke nomor terakhir, atau mulai dari 1 jika belum ada.
        //    (int) null akan menjadi 0, jadi 0 + 1 = 1.
        return (int)$lastQueueNumber + 1;
    }
}