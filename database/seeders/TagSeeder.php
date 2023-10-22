<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker\Factory::create();

        $limit = 100;

        $colors = [
            'bg-primary',
            'bg-secondary',
            'bg-success',
            'bg-danger',
            'bg-warning',
            'bg-info',
            'bg-light',
            'bg-dark',
            'bg-white'
        ];

        for ($i = 0; $i < $limit; $i++) {
            DB::table('tags')->insert([
                'name' => $faker->name,
                'color' => $faker->randomElement($colors)
            ]);
        }

        for ($i = 0; $i < $limit; $i++) {
            DB::table('product_tags')->insert([
                'product_id' => rand(1,20),
                'tag_id' => $i+1
            ]);
        }
        // for ($i = 0; $i < 50; $i++) {
        //     DB::table('product_tags')->insert([
        //         'product_id' => rand(5,10),
        //         'tag_id' => rand(1,100)
        //     ]);
        // }
        // for ($i = 0; $i < 50; $i++) {
        //     DB::table('product_tags')->insert([
        //         'product_id' => rand(10,15),
        //         'tag_id' => rand(1,100)
        //     ]);
        // }
        // for ($i = 0; $i < 50; $i++) {
        //     DB::table('product_tags')->insert([
        //         'product_id' => rand(15,20),
        //         'tag_id' => rand(1,100)
        //     ]);
        // }
    }
}
