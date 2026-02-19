<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesSerialNoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_serial_no', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_details_id');
            $table->string('serial_no')->nullable();
            $table->string('warranty_no')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_at')->nullable();
            $table->timestamps();
            
            $table->foreign('sale_details_id')
                ->references('id')
                ->on('sale_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_serial_no');
    }
}
