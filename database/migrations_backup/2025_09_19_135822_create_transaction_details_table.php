<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('transaction_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->integer('quantity')->default(1);
                $table->decimal('price', 10, 2); // Harga saat transaksi (bisa beda dari harga master)
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('transaction_details');
        }
    };