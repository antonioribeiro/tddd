<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class AddScreenshots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::table('tddd_runs', function (Blueprint $table) {
            $table->text('screenshots')->nullable();
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
        Schema::table('tddd_runs', function (Blueprint $table) {
            $table->dropColumn('screenshots');
            $table->text('png')->nullable();
        });
    }
}
