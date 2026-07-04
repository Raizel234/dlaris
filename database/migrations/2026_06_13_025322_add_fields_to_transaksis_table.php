<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->boolean('is_split')->default(false);
            $table->string('tipe_bayar', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['is_split', 'tipe_bayar']);
        });
    }
};
