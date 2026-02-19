<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class Branch extends Seeder
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

        $names = ['CSD', 'TELEMARKETING'];

        foreach ($names as $name) {
            DB::table('branches')->insert([
                'branch_name' => $name,
                'address' => 'Sample Address',
                'division_id' => 1,
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
