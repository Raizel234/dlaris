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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['persen', 'nominal', 'buy_get', 'free_ongkir'])->default('persen');
            $table->decimal('nilai', 15, 2)->default(0);
            $table->decimal('min_belanja', 15, 2)->default(0);
            $table->decimal('maks_diskon', 15, 2)->nullable();
            $table->integer('kuota')->nullable();
            $table->integer('terpakai')->default(0);
            $table->dateTime('berlaku_mulai')->nullable();
            $table->dateTime('berlaku_sampai')->nullable();
            $table->json('metode_bayar')->nullable();
            $table->json('hari')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
