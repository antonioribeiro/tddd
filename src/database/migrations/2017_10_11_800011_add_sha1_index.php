<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class AddSha1Index extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::table('tddd_tests', function (Blueprint $table) {
            $table->index('sha1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
        Schema::table('tddd_tests', function (Blueprint $table) {
            $table->dropIndex('tddd_tests_sha1_index');
        });
    }
}
