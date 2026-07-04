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
        Schema::create('bahan_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->decimal('jumlah_per_porsi', 10, 2)->default(1);
            $table->timestamps();
            $table->unique(['bahan_id', 'menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_menu');
    }
};
