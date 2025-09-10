<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // pakai locale Indonesia

        foreach (range(1, 10) as $index) {
            DB::table('products')->insert([
                'name'  => $faker->words(3, true),  // contoh nama produk random
                'price' => $faker->numberBetween(50000, 10000000), // harga 50rb - 10jt
                'stock' => $faker->numberBetween(1, 100), // stok 1 - 100
            ]);
        }
    }
}
