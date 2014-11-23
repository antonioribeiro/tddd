<?php

use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class CreateProjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::create('projects', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('name');

			$table->string('path');

			$table->string('tests_path');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function migrateDown()
	{
		Schema::drop('projects');
	}

}
