<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('clinic_settings', 'fonnte_token')) {
                $table->string('fonnte_token')->nullable()->after('phone')->comment('Token API dari Fonnte.com');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clinic_settings', function (Blueprint $table) {
            $table->dropColumn('fonnte_token');
        });
    }
};