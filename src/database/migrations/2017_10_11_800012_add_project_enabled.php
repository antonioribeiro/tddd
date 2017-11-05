<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Support\Migration;

class AddProjectEnabled extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrateUp()
    {
        Schema::table('tddd_projects', function (Blueprint $table) {
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
        Schema::table('tddd_projects', function (Blueprint $table) {
            $table->dropColumn('enabled');
        });
    }
}
