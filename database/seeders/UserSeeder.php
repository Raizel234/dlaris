<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@dlaris.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'nomor_hp' => '081234567890',
        ]);

        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@dlaris.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'nomor_hp' => '081234567891',
        ]);

        User::create([
            'name' => 'Pelanggan Demo',
            'email' => 'pelanggan@dlaris.com',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
            'nomor_hp' => '081234567892',
        ]);
    }
}
