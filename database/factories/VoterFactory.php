<?php

use Faker\Generator as Faker;

$factory->define(App\Voter::class, function (Faker $faker) {
    return [
        'firstname' => $faker->firstName,
        'middlename' => $faker->lastName,
        'lastname' => $faker->lastName,
        'extension' => $faker->suffix,
        'nickname' => $faker->userName,
        'birthday' => $faker->date,
        'contact' => $faker->phoneNumber,
        'facebook_account' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'precinct_no' => $faker->numerify('Precinct ###'),
        'occupation' => $faker->jobTitle,
        'political_affiliation' => $faker->boolean,
        'frequency_voting' => $faker->randomElement(['Always', 'Sometimes', 'Never']),
        'gender' => $faker->randomElement(['Male', 'Female']),
        'degree' => $faker->randomElement(['Bachelor', 'Master', 'PhD']),
        'address' => $faker->address,
        'house_no' => $faker->buildingNumber,
        'street_id' => 1,
        'subdivision' => $faker->streetName,
        'region_id' => $faker->stateAbbr,
        'province_id' => $faker->numberBetween(1, 100),
        'city_id' => $faker->numberBetween(1, 100),
        'barangay_id' => $faker->numberBetween(1, 100),
        'zip_code' => $faker->postcode,
        'emoney_type' => $faker->randomElement(['PayPal', 'Stripe', 'Venmo']),
        'emoney_no' => $faker->creditCardNumber,
        'candidate_id' => 1, // Assuming there are candidates with IDs 1 to 10
        'created_by' => $faker->name,
        'updated_by' => $faker->name,
        'deleted_at' => null,
    ];
});