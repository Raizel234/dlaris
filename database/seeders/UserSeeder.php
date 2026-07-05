<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@dlaris.com'], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'nomor_hp' => '081234567890',
        ]);

        User::firstOrCreate(['email' => 'kasir@dlaris.com'], [
            'name' => 'Kasir 1',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'nomor_hp' => '081234567891',
        ]);

        User::firstOrCreate(['email' => 'pelanggan@dlaris.com'], [
            'name' => 'Pelanggan Demo',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
            'nomor_hp' => '081234567892',
        ]);
    }
}
