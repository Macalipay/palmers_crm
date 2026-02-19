<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelemarketingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telemarketing_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('telemarketing_id');
            $table->string('date');
            $table->string('task')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('assigned_to');
            $table->string('status')->default('NOT STARTED');
            $table->string('remarks')->nullable();
            $table->boolean('active')->default(1);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('telemarketing_id')
                ->references('id')
                ->on('telemarketings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telemarketing_details');
    }
}
