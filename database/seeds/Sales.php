<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class Sales extends Seeder
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

        for ($i = 1; $i <= 20; $i++) {
             DB::table('sales')->insert([
                 'company_id' => $i,
                 'customer_type' => $faker->randomElement(['RETENTION', 'NEW']),
                 'source_id' => $faker->randomElement([1, 2, 3, 4, 5]),
                 'po_no' => $word_number_combo,
                 'date_purchased' => $faker->dateTimeThisMonth()->format('Y-m-d'),
                 'amount' => 0,
                 'user_id' => $faker->randomElement([2, 3]),
                 'sales_associate_id' =>$faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8, 9,10, 11, 12]),
                 'division_id' => 1,
                 'branch_id' =>  $faker->randomElement([1, 2]),
                 'date_posted' => $faker->dateTimeThisMonth()->format('Y-m-d'),
                 'agreed_delivery_date' => $faker->dateTimeThisMonth()->format('Y-m-d'),
                 'actual_delivery_date' => $faker->dateTimeThisMonth()->format('Y-m-d'),
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
