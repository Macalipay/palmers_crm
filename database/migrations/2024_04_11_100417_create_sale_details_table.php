<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('warranty_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->double('quantity', 8, 2);
            $table->double('amount', 8, 2);
            $table->double('discount', 8, 2)->default(0);
            $table->double('total', 8, 2);
            $table->boolean('active')->default(1);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('sale_id')
                ->references('id')
                ->on('sales');

            $table->foreign('item_id')
                ->references('id')
                ->on('items');

            $table->foreign('brand_id')
                ->references('id')
                ->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_details');
    }
}
