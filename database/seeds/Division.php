<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class Division extends Seeder
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

         $names = ['FPD'];

         foreach ($names as $name) {
             DB::table('divisions')->insert([
                 'division' => $name,
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
