<?php

use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class CreateTestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::create('tests', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('suite_id')->unsigned();

			$table->string('name');

			$table->string('state');

			$table->boolean('enabled')->default(true);

			$table->integer('last_run_id')->unsigned()->nullable();

			$table->timestamps();
		});

		Schema::table('tests', function(Blueprint $table)
		{
			$table->foreign('suite_id')
				->references('id')
				->on('suites')
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
		Schema::drop('tests');
	}

}
