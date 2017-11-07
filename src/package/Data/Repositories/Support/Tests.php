<?php

namespace PragmaRX\TestsWatcher\Package\Data\Repositories\Support;

use Carbon\Carbon;
use PragmaRX\TestsWatcher\Package\Data\Models\Test;
use PragmaRX\TestsWatcher\Package\Support\Constants;

trait Tests
{
    use Queue, Runs;

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
     *
     * @return boolean
     */
    public function createOrUpdateTest($file, $suite)
    {
        $test = Test::updateOrCreate(
            [
                'sha1' => sha1_file(($path = $this->normalizePath($file->getPath())).DIRECTORY_SEPARATOR.$file->getFilename()),
            ],
            [
                'path'     => $path,
                'name'     => $file->getFilename(),
                'suite_id' => $suite->id,
            ]
        );

        if ($test->wasRecentlyCreated && $this->findTestByFileAndSuite($file, $suite)) {
            $this->addTestToQueue($test);
        }

        return $test->wasRecentlyCreated;
    }

    /**
     * Sync all tests.
     *
     * @param $exclusions
     */
    public function syncTests($exclusions, $showTests)
    {
        foreach ($this->getSuites() as $suite) {
            $this->syncSuiteTests($suite, $exclusions, $showTests);
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
     * @param $run
     * @param $test
     * @param $lines
     * @param $ok
     * @param $startedAt
     * @param $endedAt
     *
     * @return mixed
     */
    public function storeTestResult($run, $test, $lines, $ok, $startedAt, $endedAt)
    {
        if (!$this->testExists($test)) {
            return false;
        }

        $run = $this->updateRun($run, $test, $lines, $ok, $startedAt, $endedAt);

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

        return $this->createNewRunForTest($test);
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

        $query = Test::select('tddd_tests.*')
                     ->join('tddd_suites', 'tddd_suites.id', '=', 'tddd_tests.suite_id');

        if ($projects && $projects != 'all') {
            $query->whereIn('tddd_suites.project_id', $projects);
        }

        if ($test_id && $test_id != 'all') {
            $query->where('tddd_tests.id', $test_id);
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

    /**
     * Update the run.
     *
     * @param $run
     * @param $test
     * @param $lines
     * @param $ok
     * @param $startedAt
     * @param $endedAt
     *
     * @return mixed
     */
    private function updateRun($run, $test, $lines, $ok, $startedAt, $endedAt)
    {
        $run->test_id = $test->id;
        $run->was_ok = $ok;
        $run->log = $this->formatLog($lines, $test) ?: '(empty)';
        $run->html = $this->getOutput($test, $test->suite->tester->output_folder,
        $test->suite->tester->output_html_fail_extension);
        $run->screenshots = $this->getScreenshots($test, $lines);
        $run->started_at = $startedAt;
        $run->ended_at = $endedAt;

        $run->save();

        return $run;
    }
}
