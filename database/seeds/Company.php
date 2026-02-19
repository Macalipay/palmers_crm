<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class Company extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $industry_values = ['GOVERNMENT', 'RESIDENTIAL', 'CORPORATE'];

        // Base names to start with
        $baseNames = ['LUFTHANSA SERVICES PHILIPPINES INC.', 'MELCO RESORTS LEISURE (PHP) CORPORATION', 
                      'THE BEACON CONDOMINIUM CORPORATION', 'GITC SUPPLY SOLUTIONS INC.', 'WEL CONTRASTING CORPORATION'];

        // Number of total records to seed
        $totalRecords = 30000;
        $batchSize = 1000; // Insert 1000 records at a time
        $companyData = [];

        for ($i = 1; $i <= $totalRecords; $i++) {
            // Generate random company names by appending numbers to base names
            $name = $faker->randomElement($baseNames) . ' ' . $i;

            // Prepare the data to be inserted
            $companyData[] = [
                'company_name' => $name,
                'contact_person' => $faker->name,
                'contact_no' => $faker->phoneNumber,
                'address' => $faker->address,
                'province_id' => $faker->numberBetween(1, 14),
                'industry' => $faker->randomElement($industry_values),
                'active' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_at' => null,
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'updated_at' => $faker->dateTimeBetween('-2 years', 'now'),
            ];

            // Insert in batches
            if (count($companyData) == $batchSize) {
                DB::table('companies')->insert($companyData);
                $companyData = []; // Reset the array for the next batch
            }
        }

        // Insert any remaining records
        if (!empty($companyData)) {
            DB::table('companies')->insert($companyData);
        }
    }
}
