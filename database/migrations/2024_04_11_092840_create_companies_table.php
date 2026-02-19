<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name');
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->string('industry')->nullable();
            $table->string('tin')->nullable();
            $table->string('business_style')->nullable();
            $table->boolean('active')->default(1);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('province_id')
                ->references('id')
                ->on('province_names');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
