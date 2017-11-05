<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class CreateTdddTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::create('tddd_tests', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('suite_id')->unsigned();

            $table->string('name');

            $table->string('state')->default('idle');

            $table->boolean('enabled')->default(true);

            $table->integer('last_run_id')->unsigned()->nullable();

            $table->timestamps();
        });

        Schema::table('tddd_tests', function (Blueprint $table) {
            $table->foreign('suite_id')
                ->references('id')
                ->on('tddd_suites')
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
        Schema::drop('tddd_tests');
    }
}
