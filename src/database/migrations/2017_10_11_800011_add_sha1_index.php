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
        Schema::table('ci_tests', function (Blueprint $table) {
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
        Schema::table('ci_tests', function (Blueprint $table) {
            $table->dropIndex('ci_tests_sha1_index');
        });
    }
}
