<?php

use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class CreateTestersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::create('testers', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('name');

			$table->string('command');

			$table->string('output_folder')->nullable();

			$table->string('output_html_fail_extension')->nullable();

			$table->string('output_png_fail_extension')->nullable();

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
		Schema::drop('testers');
	}

}
