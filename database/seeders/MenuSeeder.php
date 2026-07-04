<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['kategori_id' => 1, 'nama' => 'Nasi Goreng Spesial', 'deskripsi' => 'Nasi goreng dengan telur, ayam, dan udang', 'harga' => 35000, 'stok' => 50, 'is_tersedia' => true, 'is_best_seller' => true],
            ['kategori_id' => 1, 'nama' => 'Mie Goreng Jawa', 'deskripsi' => 'Mie goreng khas Jawa dengan bumbu rempah', 'harga' => 30000, 'stok' => 50, 'is_tersedia' => true, 'is_best_seller' => true],
            ['kategori_id' => 1, 'nama' => 'Ayam Goreng Kremes', 'deskripsi' => 'Ayam goreng dengan kremes renyah', 'harga' => 40000, 'stok' => 40, 'is_tersedia' => true],
            ['kategori_id' => 1, 'nama' => 'Sate Ayam', 'deskripsi' => 'Sate ayam dengan bumbu kacang', 'harga' => 35000, 'stok' => 30, 'is_tersedia' => true],
            ['kategori_id' => 1, 'nama' => 'Cah Kangkung', 'deskripsi' => 'Tumis kangkung segar', 'harga' => 20000, 'stok' => 40, 'is_tersedia' => true],
            ['kategori_id' => 2, 'nama' => 'Jus Alpukat', 'deskripsi' => 'Jus alpukat segar dengan susu', 'harga' => 25000, 'stok' => 50, 'is_tersedia' => true, 'is_best_seller' => true],
            ['kategori_id' => 2, 'nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis segar dengan es', 'harga' => 8000, 'stok' => 100, 'is_tersedia' => true],
            ['kategori_id' => 2, 'nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 12000, 'stok' => 80, 'is_tersedia' => true],
            ['kategori_id' => 3, 'nama' => 'French Fries', 'deskripsi' => 'Kentang goreng renyah', 'harga' => 20000, 'stok' => 50, 'is_tersedia' => true],
            ['kategori_id' => 3, 'nama' => 'Pisang Goreng', 'deskripsi' => 'Pisang goreng dengan topping coklat', 'harga' => 15000, 'stok' => 40, 'is_tersedia' => true, 'is_best_seller' => true],
            ['kategori_id' => 3, 'nama' => 'Cireng Isi', 'deskripsi' => 'Cireng dengan isian ayam suwir pedas', 'harga' => 18000, 'stok' => 35, 'is_tersedia' => true],
            ['kategori_id' => 4, 'nama' => 'Kopi Hitam', 'deskripsi' => 'Kopi hitam pilihan', 'harga' => 20000, 'stok' => 60, 'is_tersedia' => true],
            ['kategori_id' => 4, 'nama' => 'Cappuccino', 'deskripsi' => 'Kopi dengan busa susu', 'harga' => 30000, 'stok' => 50, 'is_tersedia' => true, 'is_best_seller' => true],
            ['kategori_id' => 4, 'nama' => 'Latte', 'deskripsi' => 'Kopi susu lembut', 'harga' => 30000, 'stok' => 50, 'is_tersedia' => true],
            ['kategori_id' => 4, 'nama' => 'Mocha', 'deskripsi' => 'Kopi dengan coklat', 'harga' => 35000, 'stok' => 40, 'is_tersedia' => true, 'is_new' => true],
            ['kategori_id' => 5, 'nama' => 'Mojito', 'deskripsi' => 'Mocktail mojito segar', 'harga' => 28000, 'stok' => 45, 'is_tersedia' => true, 'is_new' => true],
            ['kategori_id' => 5, 'nama' => 'Blue Lagoon', 'deskripsi' => 'Minuman biru segar', 'harga' => 30000, 'stok' => 40, 'is_tersedia' => true],
            ['kategori_id' => 5, 'nama' => 'Strawberry Smoothie', 'deskripsi' => 'Smoothie strawberry segar', 'harga' => 32000, 'stok' => 35, 'is_tersedia' => true, 'is_new' => true],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
