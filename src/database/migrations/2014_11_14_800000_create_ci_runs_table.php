<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class CreateCiRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::create('ci_runs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('test_id')->unsigned();

            $table->boolean('was_ok');

            $table->text('log');

            $table->text('html')->nullable();

            $table->text('png')->nullable();

            $table->timestamps();
        });

        Schema::table('ci_runs', function (Blueprint $table) {
            $table->foreign('test_id')
                ->references('id')
                ->on('ci_tests')
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
        Schema::drop('ci_runs');
    }
}
