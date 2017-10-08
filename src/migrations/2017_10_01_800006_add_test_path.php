<?php

use DB as Database;
use PragmaRX\Support\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

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
