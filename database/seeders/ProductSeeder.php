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

        $limit = 5;

        for ($i = 0; $i < $limit; $i++) {
            DB::table('products')->insert([
                'name' => $faker->name,
                'price' => $faker->numberBetween(10, 1000),
                'description' => $faker->paragraph,
                'excellent' => 1,
                'good' => 0,
                'bad' => 0,
                'old' => 0,
                'edition' => $faker->text(6),
                'origin_price' => $faker->numberBetween(10, 300),
                'quantity' => $faker->numberBetween(1, 10),
                'available' => 1,
                'unavailable' => 0,
                'user_id' => $faker->numberBetween(1, 10),
                'category_id' => $faker->numberBetween(1, 10),
                'transaction_id' => $faker->numberBetween(1, 2),
            ]);
        }

        for ($i = 0; $i < $limit; $i++) {
            DB::table('products')->insert([
                'name' => $faker->name,
                'price' => $faker->numberBetween(10, 1000),
                'description' => $faker->paragraph,
                'excellent' => 0,
                'good' => 1,
                'bad' => 0,
                'old' => 0,
                'edition' => $faker->text(6),
                'origin_price' => $faker->numberBetween(10, 300),
                'quantity' => $faker->numberBetween(1, 10),
                'available' => 0,
                'unavailable' => 1,
                'user_id' => $faker->numberBetween(1, 10),
                'category_id' => $faker->numberBetween(1, 10),
                'transaction_id' => $faker->numberBetween(1, 2),
            ]);
        }

        for ($i = 0; $i < $limit; $i++) {
            DB::table('products')->insert([
                'name' => $faker->name,
                'price' => $faker->numberBetween(10, 1000),
                'description' => $faker->paragraph,
                'excellent' => 0,
                'good' => 0,
                'bad' => 1,
                'old' => 0,
                'edition' => $faker->text(6),
                'origin_price' => $faker->numberBetween(10, 300),
                'quantity' => $faker->numberBetween(1, 10),
                'available' => 0,
                'unavailable' => 1,
                'user_id' => $faker->numberBetween(1, 10),
                'category_id' => $faker->numberBetween(1, 10),
                'transaction_id' => $faker->numberBetween(1, 2),
            ]);
        }

        for ($i = 0; $i < $limit; $i++) {
            DB::table('products')->insert([
                'name' => $faker->name,
                'price' => $faker->numberBetween(10, 1000),
                'description' => $faker->paragraph,
                'excellent' => 0,
                'good' => 0,
                'bad' => 0,
                'old' => 1,
                'edition' => $faker->text(6),
                'origin_price' => $faker->numberBetween(10, 300),
                'quantity' => $faker->numberBetween(1, 10),
                'available' => 1,
                'unavailable' => 0,
                'user_id' => $faker->numberBetween(1, 10),
                'category_id' => $faker->numberBetween(1, 10),
                'transaction_id' => $faker->numberBetween(1, 2),
            ]);
        }
    }
}
