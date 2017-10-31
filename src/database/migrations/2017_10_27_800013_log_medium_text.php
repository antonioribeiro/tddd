<?php

use DB as Database;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class LogMediumText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::table('ci_runs', function (Blueprint $table) {
            $table->mediumText('log_new')->nullable()->after('was_ok');;
        });

        Database::statement('update ci_runs set log_new = log;');

        Schema::table('ci_runs', function (Blueprint $table) {
            $table->dropColumn('log');
            $table->renameColumn('log_new', 'log');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
        Schema::table('ci_runs', function (Blueprint $table) {
            $table->text('log_old')->nullable();
        });

        Database::statement('update ci_runs set log_old = log;');

        Schema::table('ci_runs', function (Blueprint $table) {
            $table->dropColumn('log');
            $table->renameColumn('log_old', 'log');
        });
    }
}
