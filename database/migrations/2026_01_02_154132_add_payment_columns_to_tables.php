<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update tabel MEDICINES (Tambah Harga)
        Schema::table('medicines', function (Blueprint $table) {
            // Harga per unit obat (Rupiah)
            $table->integer('price')->default(0)->after('unit'); 
        });

        // 2. Update tabel PRESCRIPTIONS (Tambah Info Pembayaran)
        Schema::table('prescriptions', function (Blueprint $table) {
            // Status: pending (belum bayar), paid (lunas), cancelled
            $table->string('payment_status')->default('pending')->after('prescription_date');
            
            // Metode: cash, cashless (midtrans)
            $table->string('payment_method')->nullable()->after('payment_status');
            
            // Total tagihan (disimpan biar fix, gak berubah walau harga obat naik di masa depan)
            $table->bigInteger('total_price')->default(0)->after('payment_method');

            // Token & Kode Booking dari Midtrans
            $table->string('midtrans_snap_token')->nullable()->after('total_price');
            $table->string('midtrans_booking_code')->nullable()->unique()->after('midtrans_snap_token');
            
            // Waktu bayar
            $table->dateTime('paid_at')->nullable()->after('midtrans_booking_code');
        });
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'total_price',
                'midtrans_snap_token',
                'midtrans_booking_code',
                'paid_at'
            ]);
        });
    }
};