<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class User extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // use Faker\Factory as Faker;
        $faker = Faker::create();

        $names = ['R. GANTUANGCO', 'MARIVEL ARMADA'];
        $email = ['R_GANTUANGCO@GMAIL.COM', 'MARIVEL_ARMADA@GMAIL.COM'];

        foreach ($names as $index => $name) {
            DB::table('users')->insert([
                'name' => $name,
                'designation' => 'SALES AGENT',
                'email' => 'sample_' . $index . '@gmail.com',
                'contact_number' => '09759618445',
                'picture' => 'default.jpg',
                'password' => '$2y$10$SOXOFQ0FHVA/CAzE.P2bh.TE6MCoXBjDuxoqDQpkdwasYBsyUOiiC',
                'active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => null,
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'updated_at' => $faker->dateTimeBetween('-2 years', 'now'),
            ]);
        }
    }
}
