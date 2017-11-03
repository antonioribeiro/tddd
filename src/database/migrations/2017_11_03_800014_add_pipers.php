<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class AddPipers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::table('ci_testers', function (Blueprint $table) {
            $table->dropColumn('require_tee');

            $table->dropColumn('require_script');

            $table->text('pipers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
        Schema::table('ci_testers', function (Blueprint $table) {
            $table->boolean('require_tee');

            $table->boolean('require_script');

            $table->dropColumn('pipers');
        });
    }
}
