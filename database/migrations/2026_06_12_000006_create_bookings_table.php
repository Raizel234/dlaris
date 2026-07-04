<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->string('nama_pemesan');
            $table->string('nomor_hp', 20);
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('durasi');
            $table->decimal('total_harga', 12, 2);
            $table->enum('status', ['pending', 'confirmed', 'ongoing', 'selesai', 'dibatalkan'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
