<?php

use PragmaRX\Support\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

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
