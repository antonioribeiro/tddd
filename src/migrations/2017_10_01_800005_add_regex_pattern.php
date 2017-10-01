<?php

use Illuminate\Database\Schema\Blueprint;
use PragmaRX\Support\Migration;

class AddRegexPattern extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::table('ci_testers', function(Blueprint $table)
		{
			$table->string('error_pattern')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function migrateDown()
	{
        Schema::table('ci_testers', function(Blueprint $table)
        {
            $table->dropColumn('error_pattern');
        });
	}
}
