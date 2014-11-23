<?php

use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class CreateSuitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::create('suites', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('name');

			$table->integer('project_id')->unsigned();

			$table->integer('tester_id')->unsigned();

			$table->string('tests_path');

			$table->string('file_mask');

			$table->string('command_options');

			$table->integer('retries')->default(0);

			$table->timestamps();
		});

		Schema::table('suites', function(Blueprint $table)
		{
			$table->foreign('project_id')
				->references('id')
				->on('projects')
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
		Schema::drop('suites');
	}

}
