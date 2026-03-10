<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelemarketingDetailReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemarketing_detail_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('telemarketing_detail_id');
            $table->unsignedBigInteger('reported_by');
            $table->text('remarks');
            $table->string('status')->default('OPEN');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('telemarketing_detail_id')
                ->references('id')
                ->on('telemarketing_details');

            $table->foreign('reported_by')
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
        Schema::dropIfExists('telemarketing_detail_reports');
    }
}
