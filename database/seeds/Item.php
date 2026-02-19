<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class Item extends Seeder
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

         $names = ['ALARM BELL 6', 'BRAND NEW', 'DELIVERY CHARGE', 'EMERGENCY LIGHT', 'ESCUTCHEON FOR PENDENT', 'ESCUTHEON PLATE', 'ESCUTHEON PLATE FOR PENDENT',
                   'FHC WITH COMPLETE ACCESSORIES 27', 'FHC WITH COMPLETE ACCESSORIES DJ NST 32"x27"x7"', 'FIRE BLANKET', 'FIRE EXTINGHUISHER HOOK', 'FIRE HOSE 1 1/2 X 100 DJ NST',
                   'FIREMAN SUIT CABINET 27'];

        $amount = $faker->randomFloat(2, 1, 9999);
         foreach ($names as $name) {
             DB::table('items')->insert([
                 'item_name' => $name,
                 'description' => $faker->sentence,
                 'amount' => $amount,
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
