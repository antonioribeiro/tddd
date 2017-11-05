<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class CreateTdddQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::create('tddd_queue', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('test_id')->unsigned();

            $table->timestamps();
        });

        Schema::table('tddd_queue', function (Blueprint $table) {
            $table->foreign('test_id')
                ->references('id')
                ->on('tddd_tests')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
        Schema::drop('tddd_queue');
    }
}
