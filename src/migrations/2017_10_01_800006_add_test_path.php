<?php

use DB as Database;
use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class AddTestPath extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
        Database::statement('delete from ci_tests;');

        Schema::table('ci_tests', function(Blueprint $table)
		{
			$table->string('path')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function migrateDown()
	{
        Schema::table('ci_tests', function(Blueprint $table)
        {
            $table->dropColumn('path');
        });
	}
}
