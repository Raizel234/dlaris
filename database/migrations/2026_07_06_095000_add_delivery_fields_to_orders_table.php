<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('alamat_pengiriman')->nullable()->after('no_hp');
            $table->decimal('ongkir', 12, 2)->default(0)->after('alamat_pengiriman');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['alamat_pengiriman', 'ongkir']);
        });
    }
};
