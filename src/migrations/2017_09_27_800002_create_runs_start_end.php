<?php

use DB as Database;
use PragmaRX\Support\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

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
			$table->timestamp('started_at')->nullable();

            $table->timestamp('ended_at')->nullable();

            $table->timestamp('notified_at')->nullable();
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

            $table->dropColumn('notified_at');
        });
	}
}
