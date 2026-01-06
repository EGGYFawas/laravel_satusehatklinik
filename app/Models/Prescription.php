<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'medical_record_id',
        'prescription_date',
        // [PENTING] Tambahkan field pembayaran ini agar bisa diupdate
        'payment_status',       // pending, paid
        'payment_method',       // cash, midtrans
        'total_price',          // Total harga
        'amount_paid',          // [BARU] Nominal uang yang diserahkan (Cash)
        'change_amount',        // [BARU] Nominal kembalian
        'midtrans_snap_token',  // Token pembayaran
        'midtrans_booking_code',// Order ID
        'paid_at',              // Waktu bayar
    ];

    /**
     * Mendapatkan data rekam medis asal resep ini.
     */
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    /**
     * Relasi ke detail obat.
     * Nama fungsi ini SANGAT PENTING.
     * Di Controller & View kita pakai 'details', jadi di sini harus 'details'.
     */
    public function details()
    {
        return $this->hasMany(PrescriptionDetail::class, 'prescription_id');
    }

    /**
     * (Opsional) Alias jika ada kodingan lama yang pakai 'prescriptionDetails'
     * Biar aman dua-duanya jalan.
     */
    public function prescriptionDetails()
    {
        return $this->hasMany(PrescriptionDetail::class, 'prescription_id');
    }

    /**
     * [BARU] Relasi ke antrian farmasi.
     * Diperlukan untuk mengambil data petugas yang memproses obat (taken_time, user_id).
     */
    public function pharmacyQueue()
    {
        // HasOne: Karena satu resep biasanya hanya masuk satu kali ke antrian farmasi
        return $this->hasOne(PharmacyQueue::class, 'prescription_id');
    }
}