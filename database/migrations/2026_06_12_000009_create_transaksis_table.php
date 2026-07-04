<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kode_transaksi', 20)->unique();
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris'])->default('tunai');
            $table->decimal('total', 12, 2);
            $table->decimal('nominal_bayar', 12, 2)->nullable();
            $table->decimal('kembalian', 12, 2)->default(0);
            $table->enum('status', ['lunas', 'void'])->default('lunas');
            $table->text('alasan_void')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
