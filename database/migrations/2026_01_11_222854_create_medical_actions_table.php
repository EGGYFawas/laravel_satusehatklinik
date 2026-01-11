<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medical_actions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Cek Gula Darah
            $table->decimal('price', 12, 2); // Contoh: 25000.00
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_actions');
    }
};