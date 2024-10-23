<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSearchConsoleQueriesStatusesAddColumnDelegated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_console_queries_statuses', function ($table) {
            $table->tinyInteger('delegated')->after('fixed')->default(0);
            $table->integer('master_id')->after('delegated')->default(0);
            $table->string('master_comment')->after('master_id')->nullable();
            $table->integer('slave_id')->after('master_comment')->default(0);
            $table->string('slave_comment')->after('slave_id')->nullable();
            $table->tinyInteger('slave_status')->after('slave_comment')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_console_queries_statuses', function ($table) {
            $table->dropColumn('delegated');
            $table->dropColumn('master_id');
            $table->dropColumn('master_comment');
            $table->dropColumn('slave_id');
            $table->dropColumn('slave_comment');
            $table->dropColumn('slave_status');
        });
    }
}
