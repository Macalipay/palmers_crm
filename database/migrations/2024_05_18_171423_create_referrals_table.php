<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('placement_id');
            $table->string('node_address');
            $table->string('contact_no')->nullable();
            $table->string('company_name')->nullable();
            $table->string('location')->nullable();
            $table->string('location_id')->nullable();
            $table->string('status')->nullable();
            $table->string('outcome')->nullable();
            $table->string('text')->nullable();
            $table->string('active');
            $table->string('image');
            $table->string('collapsable')->default('true');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referrals');
    }
}
