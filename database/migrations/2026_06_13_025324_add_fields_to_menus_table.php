<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->decimal('rata_rating', 3, 2)->default(0)->after('is_new');
            $table->integer('total_ulasan')->default(0)->after('rata_rating');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['rata_rating', 'total_ulasan']);
        });
    }
};
