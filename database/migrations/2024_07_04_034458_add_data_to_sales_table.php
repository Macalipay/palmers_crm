<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('rfq_no')->nullable();
            $table->string('project_title')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('telephone_no')->nullable();
            $table->string('email')->nullable();
            $table->string('date_encode')->nullable();
            $table->string('date_received')->nullable();
            $table->string('date_filed')->nullable();
            $table->integer('fdas')->default(0);
            $table->integer('afss')->default(0);
            $table->integer('akfss')->default(0);
            $table->integer('fss')->default(0);
            $table->integer('supply')->default(0);
            $table->integer('pm')->default(0);
            $table->integer('cctv')->default(0);
            $table->integer('other')->default(0);
            $table->string('other_details')->nullable();
            $table->integer('floor_plan')->default(0);
            $table->integer('site_inspection')->default(0);
            $table->string('project_location')->nullable();
            $table->string('tpc')->nullable();
            $table->string('remarks')->nullable();
            $table->string('deadline')->nullable();
            $table->string('date_request')->nullable();
            $table->string('date_submitted')->nullable();
            $table->string('fsd_proposal_no')->nullable();
            $table->integer('de_supervisor')->nullable();
            $table->integer('de_engineer')->nullable();
            $table->integer('de_document_custodian')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            //
        });
    }
}
