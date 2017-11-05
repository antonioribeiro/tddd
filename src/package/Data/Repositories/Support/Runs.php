<?php

namespace PragmaRX\TestsWatcher\Package\Data\Repositories\Support;

use Illuminate\Support\Facades\DB as Database;
use PragmaRX\TestsWatcher\Package\Data\Models\Run;

trait Runs
{
    /**
     * Delete all from runs table.
     */
    public function clearRuns()
    {
        Database::statement('delete from tddd_runs');
    }

    /**
     * Create a new run record for a test.
     *
     * @param $test
     *
     * @return mixed
     */
    public function createNewRunForTest($test)
    {
        return Run::create([
            'test_id' => $test->id,
            'log'     => '',
            'was_ok'  => false,
        ]);
    }

    /**
     * Get test info.
     *
     * @param $test
     *
     * @return array
     */
    protected function getTestInfo($test)
    {
        $run = Run::where('test_id', $test->id)->orderBy('created_at', 'desc')->first();

        return [
            'id'            => $test->id,
            'suite_name'    => $test->suite->name,
            'project_name'  => $test->suite->project->name,
            'project_id'    => $test->suite->project->id,
            'path'          => $test->path.DIRECTORY_SEPARATOR,
            'name'          => $test->name,
            'edit_file_url' => $this->makeEditFileUrl($test),
            'updated_at'    => $test->updated_at->diffForHumans(null, false, true),
            'state'         => $test->state,
            'enabled'       => $test->enabled,
            'editor_name'   => $this->getEditor($test->suite)['name'],

            'run'         => $run,
            'notified_at' => is_null($run) ? null : $run->notified_at,
            'log'         => is_null($run) ? null : $run->log,
            'html'        => is_null($run) ? null : $run->html,
            'image'       => is_null($run) ? null : $run->png,
            'time'        => is_null($run) ? '' : (is_null($run->started_at) ? '' : $this->removeBefore($run->started_at->diffForHumans($run->ended_at))),
        ];
    }

    /**
     * Update the run log.
     *
     * @param $run
     * @param $output
     */
    public function updateRunLog($run, $output)
    {
        $run->log = $output;

        $run->save();
    }
}
