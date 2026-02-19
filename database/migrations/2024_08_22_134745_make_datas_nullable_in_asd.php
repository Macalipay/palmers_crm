<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeDatasNullableInAsd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_sales', function (Blueprint $table) {
            $table->string('date_purchased')->nullable()->change();
            $table->string('po_no')->nullable()->change();
            $table->string('po_amount')->nullable()->change();
            $table->string('remarks')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_sales', function (Blueprint $table) {
            $table->string('date_purchased')->nullable(false)->change();
            $table->string('po_no')->nullable(false)->change();
            $table->string('po_amount')->nullable(false)->change();
            $table->string('remarks')->nullable(false)->change();
        });
    }
}
