<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $ruangans = [
            ['nama' => 'Room A - Harmoni', 'kapasitas' => 4, 'tarif_per_jam' => 75000, 'fasilitas' => 'AC, TV 50 inch, Kipas Angin, Sofa', 'status' => 'tersedia'],
            ['nama' => 'Room B - Melodi', 'kapasitas' => 6, 'tarif_per_jam' => 100000, 'fasilitas' => 'AC, TV 55 inch, Sound System, Sofa, Meja', 'status' => 'tersedia'],
            ['nama' => 'Room C - Irama', 'kapasitas' => 8, 'tarif_per_jam' => 150000, 'fasilitas' => 'AC, TV 65 inch, Sound System Premium, Sofa, Meja, Lampu Disko', 'status' => 'tersedia'],
            ['nama' => 'Room D - Nada', 'kapasitas' => 4, 'tarif_per_jam' => 75000, 'fasilitas' => 'AC, TV 50 inch, Kipas Angin, Sofa', 'status' => 'tersedia'],
            ['nama' => 'VIP Room - Eksklusif', 'kapasitas' => 12, 'tarif_per_jam' => 250000, 'fasilitas' => 'AC, TV 75 inch, Sound System Premium, Sofa Mewah, Meja, Lampu Disko, Kursi Pijat, Mini Bar', 'status' => 'tersedia'],
        ];

        foreach ($ruangans as $ruangan) {
            Ruangan::create($ruangan);
        }
    }
}
