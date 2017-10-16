<?php

namespace PragmaRX\TestsWatcher\Package\Data\Repositories\Support;

use Carbon\Carbon;
use PragmaRX\TestsWatcher\Package\Data\Models\Run;
use PragmaRX\TestsWatcher\Package\Data\Models\Test;
use PragmaRX\TestsWatcher\Package\Support\Constants;

trait Tests
{
    use Queue;

    /**
     * Find test by filename and suite.
     *
     * @param $file
     * @param $suite
     *
     * @return mixed
     */
    protected function findTestByFileAndSuite($file, $suite)
    {
        $exists = Test::where('name', $file->getRelativePathname())
                      ->where('suite_id', $suite->id)
                      ->first();

        return $exists;
    }

    /**
     * Create or update a test.
     *
     * @param \Symfony\Component\Finder\SplFileInfo            $file
     * @param \PragmaRX\TestsWatcher\Package\Data\Models\Suite $suite
     */
    public function createOrUpdateTest($file, $suite)
    {
        $test = Test::updateOrCreate(
            [
                'sha1' => sha1_file($file->getRealPath()),
            ],
            [
                'path'     => $this->normalizePath($file->getPath()),
                'name'     => $file->getFilename(),
                'suite_id' => $suite->id,
            ]
        );

        if ($test->wasRecentlyCreated && $this->findTestByFileAndSuite($file, $suite)) {
            $this->addTestToQueue($test);
        }
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
            'updated_at'    => $test->updated_at->diffForHumans(),
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
     * Sync all tests.
     *
     * @param $exclusions
     */
    public function syncTests($exclusions)
    {
        foreach ($this->getSuites() as $suite) {
            $this->syncSuiteTests($suite, $exclusions);
        }
    }

    /**
     * Check if a file is a test file.
     *
     * @param $path
     *
     * @return \___PHPSTORM_HELPERS\static|bool|mixed
     */
    public function isTestFile($path)
    {
        if (file_exists($path)) {
            foreach (Test::all() as $test) {
                if ($test->fullPath == $path) {
                    return $test;
                }
            }
        }

        return false;
    }

    /**
     * Store the test result.
     *
     * @param $test
     * @param $lines
     * @param $ok
     * @param $startedAt
     * @param $endedAt
     *
     * @return mixed
     */
    public function storeTestResult($test, $lines, $ok, $startedAt, $endedAt)
    {
        if (!$this->testExists($test)) {
            return false;
        }

        $run = Run::create([
            'test_id'     => $test->id,
            'was_ok'      => $ok,
            'log'         => $this->formatLog($lines, $test) ?: '(empty)',
            'html'        => $this->getOutput($test, $test->suite->tester->output_folder, $test->suite->tester->output_html_fail_extension),
            'screenshots' => $this->getScreenshots($test, $lines),
            'started_at'  => $startedAt,
            'ended_at'    => $endedAt,
        ]);

        $test->state = $ok ? Constants::STATE_OK : Constants::STATE_FAILED;

        $test->last_run_id = $run->id;

        $test->save();

        $this->removeTestFromQueue($test);

        return $ok;
    }

    /**
     * Mark a test as being running.
     *
     * @param $test
     */
    public function markTestAsRunning($test)
    {
        $test->state = Constants::STATE_RUNNING;

        $test->save();
    }

    /**
     * Find a test by name and suite.
     *
     * @param $suite
     * @param $file
     *
     * @return mixed
     */
    protected function findTestByNameAndSuite($file, $suite)
    {
        return Test::where('name', $file->getRelativePathname())->where('suite_id', $suite->id)->first();
    }

    /**
     * Enable tests.
     *
     * @param $enable
     * @param $project_id
     * @param null $test_id
     *
     * @return bool
     */
    public function enableTests($enable, $project_id, $test_id)
    {
        $enable = is_bool($enable) ? $enable : ($enable === 'true');

        $tests = $this->queryTests($project_id, $test_id == 'all' ? null : $test_id)->get();

        foreach ($tests as $test) {
            $this->enableTest($enable, $test);
        }

        return $enable;
    }

    /**
     * Run a test.
     *
     * @param $test
     * @param bool $force
     */
    public function runTest($test, $force = false)
    {
        if (!$test instanceof Test) {
            $test = Test::find($test);
        }

        $this->addTestToQueue($test, $force);
    }

    /**
     * Enable a test.
     *
     * @param $enable
     * @param \PragmaRX\TestsWatcher\Package\Data\Models\Test $test
     */
    protected function enableTest($enable, $test)
    {
        $test->timestamps = false;

        $test->enabled = $enable;

        $test->save();

        if (!$enable) {
            $this->removeTestFromQueue($test);

            return;
        }

        if ($test->state !== Constants::STATE_OK) {
            $this->addTestToQueue($test);
        }
    }

    /**
     * Query tests.
     *
     * @param $test_id
     *
     * @return mixed
     */
    protected function queryTests($projects, $test_id = null)
    {
        $projects = (array) $projects;

        $query = Test::select('ci_tests.*')
                     ->join('ci_suites', 'ci_suites.id', '=', 'ci_tests.suite_id');

        if ($projects && $projects != 'all') {
            $query->whereIn('ci_suites.project_id', $projects);
        }

        if ($test_id && $test_id != 'all') {
            $query->where('ci_tests.id', $test_id);
        }

        return $query;
    }

    /**
     * Mark tests as notified.
     *
     * @param $tests
     */
    public function markTestsAsNotified($tests)
    {
        $tests->each(function ($test) {
            $test['run']->notified_at = Carbon::now();

            $test['run']->save();
        });
    }

    /**
     * Check if the test exists.
     *
     * @param $test
     *
     * @return bool
     */
    protected function testExists($test)
    {
        return !is_null(Test::find($test->id));
    }
}
