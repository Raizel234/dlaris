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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_hp', 20)->nullable();
            $table->string('alamat')->nullable();
            $table->string('foto')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->bigInteger('poin')->default(0);
            $table->integer('total_kunjungan')->default(0);
            $table->decimal('total_belanja', 15, 2)->default(0);
            $table->timestamp('terakhir_kunjungan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
