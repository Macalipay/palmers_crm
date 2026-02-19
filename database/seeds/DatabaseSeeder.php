<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('model_has_roles')->insert([
            'role_id' => 1, // Role ID
            'model_type' => 'App\User', // Model type (e.g., User)
            'model_id' => 1, // Model ID (e.g., user ID)
        ]);

        $this->call(Source::class);
        $this->call(Branch::class);
        $this->call(Brand::class);
        $this->call(Division::class);
        $this->call(Province::class);
        $this->call(SalesAssociate::class);
        $this->call(Item::class);
        $this->call(Company::class);
        $this->call(User::class);
        $this->call(Sales::class);
        $this->call(Telemarketing::class);
    }
}
