<?php

namespace Database\Seeders;

use App\Models\Meja;
use Illuminate\Database\Seeder;

class MejaSeeder extends Seeder
{
    public function run(): void
    {
        $mejas = [
            ['nomor_meja' => 'A1', 'kapasitas' => 2, 'area' => 'indoor', 'status' => 'tersedia'],
            ['nomor_meja' => 'A2', 'kapasitas' => 4, 'area' => 'indoor', 'status' => 'tersedia'],
            ['nomor_meja' => 'A3', 'kapasitas' => 4, 'area' => 'indoor', 'status' => 'tersedia'],
            ['nomor_meja' => 'A4', 'kapasitas' => 6, 'area' => 'indoor', 'status' => 'tersedia'],
            ['nomor_meja' => 'B1', 'kapasitas' => 2, 'area' => 'outdoor', 'status' => 'tersedia'],
            ['nomor_meja' => 'B2', 'kapasitas' => 4, 'area' => 'outdoor', 'status' => 'tersedia'],
            ['nomor_meja' => 'B3', 'kapasitas' => 6, 'area' => 'outdoor', 'status' => 'tersedia'],
            ['nomor_meja' => 'C1', 'kapasitas' => 8, 'area' => 'indoor', 'status' => 'tersedia'],
            ['nomor_meja' => 'C2', 'kapasitas' => 8, 'area' => 'indoor', 'status' => 'tersedia'],
            ['nomor_meja' => 'VIP', 'kapasitas' => 10, 'area' => 'indoor', 'status' => 'tersedia'],
        ];

        foreach ($mejas as $meja) {
            Meja::create($meja);
        }
    }
}
