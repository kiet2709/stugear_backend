<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker\Factory::create();

        $limit = 10;

        for ($i = 0; $i < $limit; $i++) {
            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->unique()->email,
                'password' => '$2y$10$89ZIwD/rZuyybbAm1tSXTeshyHGD1WzHhztAXLOGcu8mWFYQKzV16',
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'reputation' => $faker->numberBetween(10, 100),
                'is_enable' => 1,
                
            ]);
        }
    }
}
