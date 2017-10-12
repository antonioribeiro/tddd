<?php

namespace PragmaRX\TestsWatcher\Package\Data\Repositories\Support;

use Illuminate\Support\Facades\DB as Database;

trait Runs
{
    /**
     * Delete all from runs table.
     */
    public function clearRuns()
    {
        Database::statement('delete from ci_runs');
    }
}
