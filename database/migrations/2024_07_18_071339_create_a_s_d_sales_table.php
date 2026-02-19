<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateASDSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rfq_no');
            $table->unsignedBigInteger('source_id');
            $table->string('category');
            $table->string('customer_type');
            $table->string('project_title');
            $table->string('company_name');
            $table->text('company_address');
            $table->string('contact_person');
            $table->string('designation');
            $table->string('telephone');
            $table->string('email');
            $table->string('date_received');
            $table->string('date_filed');
            $table->text('project_location');
            $table->string('tcp');
            $table->string('deadline');
            $table->text('comments');
            $table->unsignedBigInteger('sales_associate_id')->nullable();
            $table->unsignedBigInteger('design_id')->nullable();
            $table->unsignedBigInteger('supervisor')->nullable();
            $table->string('date_submitted');
            $table->string('quoted_amount');
            $table->string('reference_no');
            $table->string('date_purchased');
            $table->string('po_no');
            $table->string('po_amount');
            $table->text('remarks');
            $table->integer('active')->default(1);
            $table->integer('type');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('source_id')
                ->references('id')
                ->on('sources');

                
            $table->foreign('sales_associate_id')
                ->references('id')
                ->on('personnels');

            $table->foreign('design_id')
                ->references('id')
                ->on('personnels');

            $table->foreign('supervisor')
                ->references('id')
                ->on('personnels');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_sales');
    }
}
