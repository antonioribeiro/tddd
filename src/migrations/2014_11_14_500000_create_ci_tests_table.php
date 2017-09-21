<?php

use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class CreateCiTestsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::create('ci_tests', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('suite_id')->unsigned();

			$table->string('name');

			$table->string('state')->default('idle');

			$table->boolean('enabled')->default(true);

			$table->integer('last_run_id')->unsigned()->nullable();

			$table->timestamps();
		});

		Schema::table('ci_tests', function(Blueprint $table)
		{
			$table->foreign('suite_id')
				->references('id')
				->on('ci_suites')
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
		Schema::drop('ci_tests');
	}
}
