<?php

use DB as Database;
use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class CreateRunsStartEnd extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
        Database::statement('delete from ci_runs;');

		Schema::table('ci_runs', function(Blueprint $table)
		{
			$table->timestamp('started_at');

            $table->timestamp('ended_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function migrateDown()
	{
        Schema::table('ci_runs', function(Blueprint $table)
        {
            $table->dropColumn('started_at');

            $table->dropColumn('ended_at');
        });
	}
}
