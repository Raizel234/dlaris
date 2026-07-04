<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('meja_id')->nullable()->constrained('mejas')->nullOnDelete();
            $table->string('nomor_order', 20)->unique();
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
