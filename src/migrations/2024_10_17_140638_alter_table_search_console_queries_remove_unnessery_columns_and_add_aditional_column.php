<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSearchConsoleQueriesRemoveUnnesseryColumnsAndAddAditionalColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_console_queries', function ($table) {
            $table->dropColumn('excluded');
            $table->dropColumn('fixed');
            $table->integer('query_status_id')->after('site_id')->default(0);
            $table->tinyInteger('low_hanging_fruit')->after('critical')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_console_queries', function ($table) {
            $table->tinyInteger('excluded')->default(0);
            $table->tinyInteger('fixed')->default(0);
            $table->dropColumn('query_status_id');
            $table->dropColumn('low_hanging_fruit');
        });
    }
}
