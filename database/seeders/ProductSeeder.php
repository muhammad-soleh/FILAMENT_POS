<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $produkWarung = [
            'Indomie Goreng',
            'Indomie Ayam Bawang',
            'Mie Sedap Kari Ayam',
            'Beras 1 Kg',
            'Minyak Goreng 1 Liter',
            'Gula Pasir 1 Kg',
            'Garam Dapur',
            'Telur Ayam 1 Butir',
            'Kopi Sachet',
            'Teh Celup',
            'Air Mineral Botol',
            'Susu Kental Manis',
            'Biskuit Kaleng',
            'Roti Tawar',
            'Permen Karet',
            'Snack Ring',
            'Keripik Kentang',
            'Sabun Mandi',
            'Shampo Sachet',
            'Pasta Gigi',
        ];

        foreach ($produkWarung as $index => $produk) {
            DB::table('products')->insert([
                'name' => $produk,
                'price' => rand(1, 50) * 1000, // 1000 - 50000
                'stock' => rand(5, 100),
            ]);
        }
    }
}
