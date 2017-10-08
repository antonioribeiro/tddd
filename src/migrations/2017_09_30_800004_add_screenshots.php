<?php

use PragmaRX\Support\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddScreenshots extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function migrateUp()
	{
		Schema::table('ci_runs', function(Blueprint $table)
		{
			$table->json('screenshots')->nullable();
            $table->dropColumn('png');
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
            $table->dropColumn('screenshots');
            $table->text('png')->nullable();
        });
	}
}
