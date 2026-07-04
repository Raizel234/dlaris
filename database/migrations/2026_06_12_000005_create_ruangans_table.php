<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('kapasitas');
            $table->decimal('tarif_per_jam', 12, 2);
            $table->text('fasilitas')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['tersedia', 'digunakan', 'maintenance'])->default('tersedia');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};
