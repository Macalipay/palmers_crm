<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCallDurationToTelemarketingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telemarketing_details', function (Blueprint $table) {
            $table->string('call_duration')->nullable();
            $table->string('assigned_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telemarketing_details', function (Blueprint $table) {
            //
        });
    }
}
