<?php

namespace PragmaRX\Tddd\Package\Services;

use Closure;
use PragmaRX\Tddd\Package\Console\Commands\BaseCommand as Command;
use PragmaRX\Tddd\Package\Data\Repositories\Data as DataRepository;
use PragmaRX\Tddd\Package\Support\Executor;
use Symfony\Component\Process\Process;

class Tester extends Base
{
    /**
     * Is it testing?
     *
     * @var
     */
    protected $testing;

    /**
     * The data repository.
     *
     * @object \PragmaRX\Tddd\Package\Data\Repositories\Data
     */
    protected $dataRepository;

    /**
     * The piped file.
     *
     * @var string
     */
    private $pipedFile;

    /**
     * The shell executor.
     *
     * @var \PragmaRX\Tddd\Package\Support\Executor
     */
    private $executor;

    /**
     * Instantiate a Tester.
     *
     * @param DataRepository $dataRepository
     * @param Executor       $executor
     */
    public function __construct(DataRepository $dataRepository, Executor $executor)
    {
        $this->dataRepository = $dataRepository;

        $this->executor = $executor;
    }

    private function addPiper($piper, $command)
    {
        return str_replace(
            [
                '{$bin}',
                '{$tempFile}',
                '{$command}',
            ],

            [
                $piper['bin'],
                $this->pipedFile = tempnam($this->config('root.tmp_dir'), 'tw-'),
                $command,
            ],

            $piper['execute']
        );
    }

    /**
     * Add the command responsible for piping the output.
     *
     * @param $test
     *
     * @return string
     */
    private function addPiperCommand($test)
    {
        $command = collect($test->suite->tester->pipers)->reduce(function ($carry, $piper) use ($test) {
            return $this->addPiper($piper, $carry);
        }, $test->testCommand);

        return trim("{$test->env} {$command}");
    }

    /**
     * Delete temporary tee file.
     */
    private function deleteTeeTempFile()
    {
        if (!is_null($this->pipedFile) && file_exists($this->pipedFile)) {
            unlink($this->pipedFile);
        }
    }

    /**
     * Get the output from pipe or Process.
     *
     * @param $buffer \Symfony\Component\Process\Process
     * @param $test
     *
     * @return bool|string
     */
    private function getOutput($buffer, $test)
    {
        $piped = $this->wasPiped($test) ? $this->getPipedFileContents() : '';

        $buffer = $buffer instanceof Process ? $buffer->getOutput() : $buffer;

        return $piped ?: $buffer;
    }

    /**
     * Get the output from the pipe file.
     *
     * @return bool|string
     */
    private function getPipedFileContents()
    {
        return file_get_contents($this->pipedFile);
    }

    /**
     * Run the tester.
     *
     * @param \PragmaRX\Tddd\Package\Console\Commands\BaseCommand $command
     */
    public function run(Command $command)
    {
        $this->setCommand($command);

        $this->showProgress($this->config('root.names.worker'), 'info');

        $this->startTester();
    }

    /**
     * Check if the output must be piped.
     *
     * @param $test
     *
     * @return bool
     */
    private function shouldPipe($test)
    {
        return $test->suite->tester->pipers->count() > 0;
    }

    /**
     * Start the timed tester.
     *
     * @param int     $interval - Defaults to 100ms between tests.
     * @param null    $timeout
     * @param Closure $callback
     */
    public function startTester($interval = 100000, $timeout = null, Closure $callback = null)
    {
        $this->testing = true;

        $is_idle = false;

        $timeTesting = 0;

        while ($this->testing) {
            if (is_callable($callback)) {
                call_user_func($callback, $this);
            }

            usleep($interval);

            if (!$this->test()) {
                if (!$is_idle) {
                    $is_idle = true;

                    $this->command->info('idle...');
                }
            } else {
                $is_idle = false;
            }

            $timeTesting += $interval;

            if (!is_null($timeout) and $timeTesting >= $timeout) {
                $this->stopTester();
            }
        }
    }

    /**
     * Stop testing.
     *
     * @return void
     */
    public function stopTester()
    {
        $this->testing = false;
    }

    /**
     * Find and execute a test.
     */
    private function test()
    {
        if (!$test = $this->dataRepository->getNextTestFromQueue()) {
            return false;
        }

        $ok = false;

        $lines = '';

        $run = $this->dataRepository->markTestAsRunning($test);

        $command = replace_suite_paths($test->suite, $this->addPiperCommand($test));

        chdir($test->suite->project->path);

        $this->showProgress('RUNNING: '.$command.' - at '.$test->suite->project->path.' - cwd:'.getcwd(), 'comment');

        $logOutput = '';

        for ($times = 0; $times <= $test->suite->retries; $times++) {
            if ($times > 0) {
                $this->showProgress('retrying...');
            }

            $process = $this->executor->exec($command, $test->suite->project->path, function ($type, $buffer) use ($run, $test, &$logOutput) {
                $logOutput .= $buffer;

                $this->dataRepository->updateRunLog($run, $this->getOutput($this->dataRepository->formatLog($logOutput, $test), $test));

                if ($this->config('root.show_progress')) {
                    $this->showProgress($buffer);
                }
            });

            $lines = $this->getOutput($process, $test);

            if ($ok = $this->testPassed($process->getExitCode(), $test)) {
                break;
            }
        }

        $this->command->{$ok ? 'info' : 'error'}($ok ? 'OK' : 'FAILED');

        $this->dataRepository->storeTestResult($run, $test, $lines, $ok, $this->executor->startedAt, $this->executor->endedAt);

        $this->deleteTeeTempFile();

        return true;
    }

    /**
     * Check if the test has passed.
     *
     * @param $exitCode
     * @param \PragmaRX\Tddd\Package\Data\Models\Test $test
     *
     * @return bool
     */
    private function testPassed($exitCode, $test)
    {
        if ($exitCode !== 0) {
            return false;
        }

        if (!$this->shouldPipe($test) || empty($test->suite->tester->error_pattern)) {
            return true;
        }

        preg_match_all(
            "/{$test->suite->tester->error_pattern}/",
            $this->dataRepository->removeAnsiCodes($this->getPipedFileContents()),
            $matches,
            PREG_SET_ORDER
        );

        return count($matches) == 0;
    }

    /**
     * Check if the test was piped.
     *
     * @param \PragmaRX\Tddd\Package\Data\Models\Test $test
     *
     * @return bool
     */
    private function wasPiped($test)
    {
        return $this->shouldPipe($test) && file_exists($this->pipedFile);
    }
}
