<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchConsoleQueryPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_console_query_pages', function (Blueprint $table) {
            $table->id();
            $table->string('query_id',255);
            $table->string('page');
            $table->float('clicks');
            $table->float('impressions');
            $table->float('ctr');
            $table->float('position');
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
        Schema::dropIfExists('search_console_query_pages');
    }
}
