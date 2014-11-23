<?php

use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password', 60);
			$table->rememberToken();
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
		Schema::drop('users');
	}

}
