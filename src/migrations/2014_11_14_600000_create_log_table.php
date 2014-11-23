<?php

use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class CreateLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::create('log', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('test_id')->unsigned();

			$table->string('was_green');

			$table->text('log');

			$table->timestamps();
		});

		Schema::table('log', function(Blueprint $table)
		{
			$table->foreign('test_id')
				->references('id')
				->on('tests')
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
		Schema::drop('log');
	}

}
