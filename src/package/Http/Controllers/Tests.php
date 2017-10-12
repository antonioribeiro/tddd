<?php

namespace PragmaRX\TestsWatcher\Package\Http\Controllers;

class Tests extends Controller
{
    /**
     * Reset all tests.
     *
     * @param $project_id
     *
     * @return mixed
     */
    public function reset($project_id)
    {
        $this->dataRepository->reset($project_id);

        return $this->success();
    }

    /**
     * Run a test.
     *
     * @param $test_id
     *
     * @return mixed
     */
    public function run($test_id)
    {
        $this->dataRepository->runTest($test_id);

        return $this->success();
    }

    /**
     * Run all tests.
     *
     * @param $project_id
     *
     * @return mixed
     */
    public function runAll($project_id)
    {
        $this->dataRepository->runAll($project_id);

        return $this->success();
    }

    /**
     * Enable tests.
     *
     * @param $enable
     * @param $project_id
     * @param null $test_id
     *
     * @return mixed
     */
    public function enable($enable, $project_id, $test_id = null)
    {
        $enabled = $this->dataRepository->enableTests($enable, $project_id, $test_id);

        return $this->success(['enabled' => $enabled]);
    }
}
