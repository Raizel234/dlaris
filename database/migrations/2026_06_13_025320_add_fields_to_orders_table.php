<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('promo_id')->nullable()->after('meja_id')->constrained()->nullOnDelete();
            $table->decimal('diskon', 15, 2)->default(0)->after('total');
            $table->decimal('total_setelah_diskon', 15, 2)->default(0)->after('diskon');
            $table->decimal('pajak', 15, 2)->default(0)->after('total_setelah_diskon');
            $table->decimal('service_charge', 15, 2)->default(0)->after('pajak');
            $table->decimal('grand_total', 15, 2)->default(0)->after('service_charge');
            $table->enum('tipe_pesanan', ['dine_in', 'takeaway', 'delivery'])->default('dine_in')->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('promo_id');
            $table->dropColumn(['diskon', 'total_setelah_diskon', 'pajak', 'service_charge', 'grand_total', 'tipe_pesanan']);
        });
    }
};
