<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // DB::statement('SET SESSION sql_require_primary_key = OFF;');
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('picture')->default('default.jpg');
            $table->string('password');
            $table->rememberToken();
            $table->boolean('active')->default(1);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_at')->nullable();
            $table->timestamps();
        });

        DB::table('users')->insert([
            [
                'name' => 'Super Admin',
                'designation' => 'Administrator',
                'email' => 'superadmin@gmail.com',
                'contact_number' => '',
                'picture' => 'default.jpg',
                'password' => Hash::make('P@ssw0rd')
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
