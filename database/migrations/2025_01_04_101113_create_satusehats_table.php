<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('satusehats', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['AKTIF', 'TIDAK AKTIF'])->default('TIDAK AKTIF');
            $table->enum('environment', ['TESTING', 'PRODUCTION'])->default('TESTING');
            $table->string('organization_id')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satusehats');
    }
};
