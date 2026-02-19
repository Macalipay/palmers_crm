<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SalesAssociate extends Seeder
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

         $names = ['AERO - PACK INDUSTRIES', 'ALIW CINEMA COMPLEX INC.', 'ALL FRESHMART INC.', 'BENITA FELIX', 'CLEAN AND CRISP LAUNDROMAT', 'CONNIE GREY',
                   'CORPORATE', 'CRISAIR INDL INC.', 'CRM', 'CSD', 'FACYTECH SIGNS & CONSTRUCTION', 'FEDSAL DEVELOPMENT CORP'];

         foreach ($names as $name) {
             DB::table('sales_associates')->insert([
                 'sales_associate' => $name,
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
