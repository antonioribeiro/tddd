<?php

use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class CreatePasswordResetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::create('password_resets', function(Blueprint $table)
		{
			$table->string('email')->index();
			$table->string('token')->index();
			$table->timestamp('created_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function migrateDown()
	{
		Schema::drop('password_resets');
	}

}
