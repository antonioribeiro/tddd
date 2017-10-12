<?php

use PragmaRX\Support\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddProjectEnabled extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::table('ci_projects', function (Blueprint $table) {
            $table->boolean('enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function migrateDown()
    {
        Schema::table('ci_projects', function (Blueprint $table) {
            $table->dropColumn('enabled');
        });
    }
}
