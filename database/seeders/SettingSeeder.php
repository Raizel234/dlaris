<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'nama_aplikasi', 'value' => "D'LARIS Cafe & Karaoke"],
            ['key' => 'deskripsi', 'value' => 'Cafe dan Karaoke terbaik di kota'],
            ['key' => 'alamat', 'value' => 'Jl. Raya Utama No. 123'],
            ['key' => 'telepon', 'value' => '08123456789'],
            ['key' => 'email', 'value' => 'info@dlaris.com'],
            ['key' => 'jam_buka', 'value' => '10:00'],
            ['key' => 'jam_tutup', 'value' => '23:00'],
            ['key' => 'ppn', 'value' => '10'],
            ['key' => 'service_charge', 'value' => '5'],
            ['key' => 'mata_uang', 'value' => 'Rp'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
