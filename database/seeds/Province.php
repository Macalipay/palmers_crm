<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class Province extends Seeder
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

         $names = ['ALBAY', 'ANTIQUE', 'BATANGAS', 'BULACAN', 'CAGAYAN', 'CAVITE', 'CEBU', 'DAVAO DEL SUR', 'LAGUNA', 'MARINDUQUE', 'METRO MANILA', 'MINDORO OCCIDENTAL', 'RIZAL', 'SORSOGON'];

         foreach ($names as $name) {
             DB::table('province_names')->insert([
                 'province' => $name,
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
