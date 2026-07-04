<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Makanan', 'deskripsi' => 'Menu makanan utama', 'ikon' => 'fa-utensils', 'is_active' => true],
            ['nama' => 'Minuman', 'deskripsi' => 'Minuman segar dan hangat', 'ikon' => 'fa-mug-hot', 'is_active' => true],
            ['nama' => 'Snack', 'deskripsi' => 'Camilan ringan', 'ikon' => 'fa-cookie', 'is_active' => true],
            ['nama' => 'Coffee', 'deskripsi' => 'Kopi spesial', 'ikon' => 'fa-mug-saucer', 'is_active' => true],
            ['nama' => 'Mocktail', 'deskripsi' => 'Minuman non-alkohol spesial', 'ikon' => 'fa-glass-water', 'is_active' => true],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }
    }
}
