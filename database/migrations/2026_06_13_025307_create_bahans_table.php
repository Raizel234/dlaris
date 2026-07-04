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
        Schema::create('bahans', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->string('satuan', 20);
            $table->decimal('stok', 12, 2)->default(0);
            $table->decimal('stok_minimum', 12, 2)->default(0);
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->string('supplier')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahans');
    }
};
