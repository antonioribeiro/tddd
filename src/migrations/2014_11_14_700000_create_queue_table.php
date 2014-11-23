<?php

use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class CreateQueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::create('queue', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('test_id')->unsigned();

			$table->timestamps();
		});

		Schema::table('queue', function(Blueprint $table)
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
		Schema::drop('queue');
	}

}
