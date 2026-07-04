<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KategoriSeeder::class,
            MenuSeeder::class,
            MejaSeeder::class,
            RuanganSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
