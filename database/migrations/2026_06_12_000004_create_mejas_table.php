<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mejas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_meja', 10)->unique();
            $table->integer('kapasitas');
            $table->enum('area', ['indoor', 'outdoor'])->default('indoor');
            $table->enum('status', ['tersedia', 'terisi', 'reserved'])->default('tersedia');
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mejas');
    }
};
