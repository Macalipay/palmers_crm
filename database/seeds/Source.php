<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class Source extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();


        $names = ['CRM', 'PAE', 'PHONE IN', 'WALK-IN', 'WEB INQUIRY'];

        foreach ($names as $name) {
            DB::table('sources')->insert([
                'source' => $name,
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
