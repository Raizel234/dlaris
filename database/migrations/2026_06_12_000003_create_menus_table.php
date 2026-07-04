<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2);
            $table->integer('stok')->default(0);
            $table->string('foto')->nullable();
            $table->boolean('is_tersedia')->default(true);
            $table->boolean('is_best_seller')->default(false);
            $table->boolean('is_new')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
