<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResolutionFieldsToTelemarketingDetailReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telemarketing_detail_reports', function (Blueprint $table) {
            $table->text('resolution_remarks')->nullable()->after('status');
            $table->unsignedBigInteger('resolved_by')->nullable()->after('resolution_remarks');
            $table->timestamp('resolved_at')->nullable()->after('resolved_by');

            $table->foreign('resolved_by')
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
        Schema::table('telemarketing_detail_reports', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropColumn(['resolution_remarks', 'resolved_by', 'resolved_at']);
        });
    }
}
