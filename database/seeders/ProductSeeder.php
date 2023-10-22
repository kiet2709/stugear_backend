<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker\Factory::create();

        $limit = 20;

        for ($i = 0; $i < $limit; $i++) {
            DB::table('products')->insert([
                'name' => $faker->name,
                'price' => $faker->numberBetween(10000,1000000),
                'description' => $faker->paragraph,
                'condition' => $faker->randomElement(['0','1']),
                'edition' => $faker->text(6),
                'origin_price' => $faker->numberBetween(10, 300),
                'quantity' => $faker->numberBetween(1, 10),
                'status' => $faker->randomElement(['0','1']),
                'user_id' => $faker->numberBetween(1, 10),
                'category_id' => $faker->numberBetween(1, 10),
                'transaction_id' => $faker->numberBetween(1, 2),
            ]);
        }
    }
}
