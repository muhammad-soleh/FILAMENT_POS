<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CostumerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // locale Indonesia

        foreach (range(1, 10) as $index) {
            DB::table('costumers')->insert([
                'name'    => $faker->name,
                'address' => $faker->address,
                'phone'   => $faker->numerify('08##########'),
            ]);
        }
    }
}
