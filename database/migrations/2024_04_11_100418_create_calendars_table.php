<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('telemarketing_detail_id')->nullable();
            $table->date('start');
            $table->date('end');
            $table->string('color')->default('blue');
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->string('reminder')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('sale_id')
                ->references('id')
                ->on('sales');

            $table->foreign('telemarketing_detail_id')
                ->references('id')
                ->on('telemarketing_details');

            $table->foreign('created_by')
                ->references('id')
                ->on('users');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendars');
    }
}
