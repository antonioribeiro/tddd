<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class CreateTdddRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::create('tddd_runs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('test_id')->unsigned();

            $table->boolean('was_ok');

            $table->mediumText('log');

            $table->text('html')->nullable();

            $table->text('png')->nullable();

            $table->timestamps();
        });

        Schema::table('tddd_runs', function (Blueprint $table) {
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
        Schema::drop('tddd_runs');
    }
}
