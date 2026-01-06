<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            // Menyimpan nominal uang yang diserahkan pasien
            $table->decimal('amount_paid', 15, 2)->nullable()->after('total_price');
            // Menyimpan nominal kembalian
            $table->decimal('change_amount', 15, 2)->nullable()->after('amount_paid');
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'change_amount']);
        });
    }
};