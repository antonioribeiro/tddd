<?php

namespace PragmaRX\TestsWatcher\Package\Data\Repositories\Support;

use PragmaRX\TestsWatcher\Package\Data\Models\Queue as QueueModel;
use PragmaRX\TestsWatcher\Package\Data\Models\Test;
use PragmaRX\TestsWatcher\Package\Support\Constants;

trait Queue
{
    /**
     * Is the test in the queue?
     *
     * @param $test
     *
     * @return bool
     */
    public function isEnqueued($test)
    {
        return
            $test->state == Constants::STATE_QUEUED
            &&
            QueueModel::where('test_id', $test->id)->first();
    }

    /**
     * Queue all tests.
     */
    public function queueAllTests()
    {
        $this->showProgress('QUEUE: adding tests to queue...');

        foreach (Test::all() as $test) {
            $this->addTestToQueue($test);
        }
    }

    /**
     * Queue all tests from a particular suite.
     *
     * @param $suite_id
     */
    public function queueTestsForSuite($suite_id)
    {
        $tests = Test::where('suite_id', $suite_id)->get();

        foreach ($tests as $test) {
            $this->addTestToQueue($test);
        }
    }

    /**
     * Add a test to the queue.
     *
     * @param $test
     * @param bool $force
     */
    public function addTestToQueue($test, $force = false)
    {
        if ($test->enabled && $test->suite->project->enabled && !$this->isEnqueued($test)) {
            $test->updateSha1();

            QueueModel::updateOrCreate(['test_id' => $test->id]);

            if ($force || !in_array($test->state, [Constants::STATE_RUNNING, Constants::STATE_QUEUED])) {
                $test->state = Constants::STATE_QUEUED;
                $test->timestamps = false;
                $test->save();
            }
        }
    }

    /**
     * Get a test from the queue.
     *
     * @return \PragmaRX\TestsWatcher\Package\Data\Models\Test|null
     */
    public function getNextTestFromQueue()
    {
        $query = QueueModel::join('tddd_tests', 'tddd_tests.id', '=', 'tddd_queue.test_id')
                      ->where('tddd_tests.enabled', true)
                      ->where('tddd_tests.state', '!=', Constants::STATE_RUNNING);

        if (!$queue = $query->first()) {
            return;
        }

        return $queue->test;
    }

    /**
     * Remove test from que run queue.
     *
     * @param $test
     *
     * @return mixed
     */
    protected function removeTestFromQueue($test)
    {
        QueueModel::where('test_id', $test->id)->delete();

        return $test;
    }

    /**
     * Reset a test to idle state.
     *
     * @param $test
     */
    protected function resetTest($test)
    {
        QueueModel::where('test_id', $test->id)->delete();

        $test->state = Constants::STATE_IDLE;

        $test->timestamps = false;

        $test->save();
    }
}
