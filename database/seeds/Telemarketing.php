<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class Telemarketing extends Seeder
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

       $word = $faker->word();
       $number = mt_rand(0, 9);
       $amount = $faker->randomFloat(2, 1, 9999);
       $word_number_combo = $word . $number;

       $fire_products = [
        'Fire Extinguisher',
        'Fire Alarm System',
        'Fire Hydrant',
        'Smoke Detector',
        'Fire Blanket',
        'Sprinkler System',
        'Fire Escape Ladder',
        'Flame Retardant Clothing',
        'Fire Hose',
        'Emergency Lighting',
        'Fire-resistant Doors',
        'Heat Detector',
        'Fire Safety Signage',
        'Fire-proof Safe',
        'Fireproofing Materials'
    ];

       for ($i = 1; $i <= 20; $i++) {
            DB::table('telemarketings')->insert([
                'company_id' => $i,
                'lead_status' => $faker->randomElement(['PROSPECT', 'ENGAGE', 'ACQUIRE', 'RETENTION']),
                'opportunity_status' => $faker->randomElement(['OPEN', 'CLOSED', 'DEAL', 'LOST']),
                'source_id' =>  $faker->randomElement([1, 2, 3, 4, 5]),
                'product_interest' => $faker->randomElement($fire_products),
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
