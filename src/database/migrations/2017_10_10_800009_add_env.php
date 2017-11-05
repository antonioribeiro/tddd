<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class AddEnv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::table('tddd_testers', function (Blueprint $table) {
            $table->string('env')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
        Schema::table('tddd_testers', function (Blueprint $table) {
            $table->dropColumn('env');
        });
    }
}
