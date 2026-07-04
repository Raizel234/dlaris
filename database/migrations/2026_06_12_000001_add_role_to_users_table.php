<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'kasir', 'karyawan', 'pelanggan'])->default('pelanggan')->after('email');
            $table->string('nomor_hp', 20)->nullable()->after('role');
            $table->string('foto')->nullable()->after('nomor_hp');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'nomor_hp', 'foto']);
            $table->dropSoftDeletes();
        });
    }
};
