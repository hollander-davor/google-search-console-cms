<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchConsoleQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_console_queries', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('site_id');
            $table->string('query',255);
            $table->float('clicks');
            $table->float('impressions');
            $table->float('ctr');
            $table->float('position');
            $table->tinyInteger('excluded')->default(0);
            $table->tinyInteger('fixed')->default(0);
            $table->tinyInteger('critical')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_console_queries');
    }
}
