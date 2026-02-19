<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->string('customer_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('po_no')->nullable()->nullable();
            $table->string('date_purchased')->nullable();
            $table->string('amount')->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sales_associate_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('date_posted')->nullable();
            $table->string('agreed_delivery_date')->nullable();
            $table->string('actual_delivery_date')->nullable();
            $table->string('payment_term')->nullable();
            $table->boolean('active')->default(1);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('division_id')
                ->references('id')
                ->on('divisions');

            $table->foreign('sales_associate_id')
                ->references('id')
                ->on('sales_associates');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies');

            $table->foreign('source_id')
                ->references('id')
                ->on('sources');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
