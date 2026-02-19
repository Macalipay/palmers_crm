<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class Brand extends Seeder
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

         $names = ['AMEREX', 'ASAHI', 'FIRE RETARDANT', 'FIRELITE', 'FLORIAN', 'GLOBE', 'GST', 'ORBIK', 'PALMER'];

         foreach ($names as $name) {
             DB::table('brands')->insert([
                 'brand' => $name,
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
