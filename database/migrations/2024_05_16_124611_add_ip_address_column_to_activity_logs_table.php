<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIpAddressColumnToActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('activity_logs', 'ip_address')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('ip_address')->nullable();
            });
        }

        // Check if the 'device_info' column does not exist
        if (!Schema::hasColumn('activity_logs', 'device_info')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('device_info')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the 'ip_address' column if it exists
        if (Schema::hasColumn('activity_logs', 'ip_address')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('ip_address');
            });
        }

        // Drop the 'device_info' column if it exists
        if (Schema::hasColumn('activity_logs', 'device_info')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('device_info');
            });
        }
    }
}
