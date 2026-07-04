<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'kasir', 'karyawan', 'pelanggan') NOT NULL DEFAULT 'pelanggan'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'kasir', 'pelanggan') NOT NULL DEFAULT 'pelanggan'");
    }
};
