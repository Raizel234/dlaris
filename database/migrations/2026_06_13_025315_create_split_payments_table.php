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
        Schema::create('split_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris', 'kartu']);
            $table->decimal('jumlah', 15, 2);
            $table->decimal('nominal_bayar', 15, 2);
            $table->decimal('kembalian', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('split_payments');
    }
};
