<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker;
use Illuminate\Support\Facades\DB;

class ContactDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker\Factory::create();

        $limit = 10;

        $socialMediaDomains = [
            'facebook.com',
            'twitter.com',
            'instagram.com',
            'linkedin.com',
            // Add more social media domains as needed
        ];

        for ($i = 0; $i < $limit; $i++) {
            DB::table('contact_details')->insert([
                'phone_number' => $faker->phoneNumber,
                'gender' => $faker->numberBetween(0,1),
                'birthdate' => $faker->date('Y-m-d'),
                'full_address' => $faker->address,
                'province' => $faker->text(100),
                'ward' => $faker->text(100),
                'district' => $faker->text(100),
                'city' => $faker->text(100),
                'social_link' => 'https://www.' . $faker->randomElement($socialMediaDomains) . '/' . $faker->userName,
                'user_id' => $i+1
            ]);
        }
    }
}
