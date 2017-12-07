<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class AddEditor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::table('tddd_suites', function (Blueprint $table) {
            $table->string('coverage_enabled')->boolean(false);

            $table->string('coverage_index')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
        Schema::table('tddd_suites', function (Blueprint $table) {
            $table->dropColumn('coverage_enabled');

            $table->dropColumn('coverage_index');
        });
    }
}
