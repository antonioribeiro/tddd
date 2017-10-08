<?php

namespace PragmaRX\TestsWatcher\Services;

use Illuminate\Console\Command;
use PragmaRX\TestsWatcher\Support\ShellExec;
use PragmaRX\TestsWatcher\Data\Repositories\Data as DataRepository;

class Tester extends Base {

	/**
	 * Is it testing?
	 *
	 * @var
	 */
	protected $testing;

	/**
	 * The command object.
	 *
	 * @object Illuminate\Console\Command
	 */
	protected $command;

	/**
	 * @var ShellExec
	 */
	private $shell;

    private $pipedFile;

    /**
     * Instantiate a Tester.
     *
     * @param DataRepository $dataRepository
     * @param ShellExec $shell
     */
	public function __construct(DataRepository $dataRepository, ShellExec $shell)
	{
		$this->dataRepository = $dataRepository;

		$this->shell = $shell;
	}

    private function addPiperCommand($test)
    {
        if ($test->suite->tester->require_tee) {
            return $this->addTee($test->testCommand);
        }

        if ($test->suite->tester->require_script) {
            return $this->addScript($test->testCommand);
        }

        return $test->testCommand;
    }

    /**
     * Add tee to test command.
     *
     * @param $testCommand
     * @return string
     * @internal param $test
     */
    private function addTee($testCommand)
    {
        $this->pipedFile = null;

        if ($tee = $this->getConfig('tee')) {
            $this->pipedFile = tempnam($this->getConfig('tmp'), 'tw-');

            $testCommand .= " | {$tee} > {$this->pipedFile}";
        }

        return $testCommand;
    }

    /**
     * Add tee to test command.
     *
     * @param $testCommand
     * @return string
     */
    private function addScript($testCommand)
    {
        $this->pipedFile = null;

        if ($script = $this->getConfig('script')) {
            $testCommand = sprintf(
                $script,
                $this->pipedFile = tempnam($this->getConfig('tmp'), 'tw-'),
                $testCommand
            );
        }

        return $testCommand;
    }

    /**
     * Delete temporary tee file.
     *
     */
    private function deleteTeeTempFile()
    {
        if (!is_null($this->pipedFile) && file_exists($this->pipedFile)) {
            unlink($this->pipedFile);
        }
    }

    private function getOutput($process, $test)
    {
        if ($this->wasPiped($test)) {
            return $this->getPipedFileContents();
        }

        return $process->getOutput();
    }

    /**
     * @return bool|string
     */
    private function getPipedFileContents()
    {
        return file_get_contents($this->pipedFile);
    }

    /**
	 * Run the tester.
	 *
	 * @param Command $command
	 */
	public function run(Command $command)
	{
		$this->command = $command;

		$this->command->comment($this->getConfig('names.worker'));

		$this->startTester();
	}

    /**
     * @param $test
     * @return bool
     */
    private function shouldPipe($test)
    {
        return ($test->suite->tester->require_tee ||
            $test->suite->tester->require_script);
    }

    /**
	 * Start the timed tester.
	 *
	 * @param int $interval - Defaults to 100ms between tests.
	 * @param null $timeout
	 * @param Closure $callback
	 */
	public function startTester($interval = 100000, $timeout = null, Closure $callback = null)
	{
		$this->testing = true;

		$is_idle = false;

		$timeTesting = 0;

		while ($this->testing)
		{
			if (is_callable($callback))
			{
				call_user_func($callback, $this);
			}

			usleep($interval);

			if ( ! $this->test())
			{
				if ( ! $is_idle)
				{
					$is_idle = true;

					$this->command->info('idle...');
				}
			}
			else
			{
				$is_idle = false;
			}

			$timeTesting += $interval;

			if ( ! is_null($timeout) and $timeTesting >= $timeout)
			{
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
	 *
	 */
	private function test()
	{
		if (!$test = $this->dataRepository->getNextTestFromQueue())
		{
			return false;
		}

        $this->dataRepository->markTestAsRunning($test);

		$command = $this->addPiperCommand($test);

		$this->command->drawLine($line = 'Executing '.$command);

		$this->command->line($line);

        foreach(range(0, $test->suite->retries-1) as $item)
		{
			$process = $this->shell->exec($command, $test->suite->project->path, function($type, $buffer)
			{
				if ($this->getConfig('show_progress'))
				{
					$this->showProgress($buffer);
				}
			});

            $lines = $this->getOutput($process, $test);

			if ($ok = $this->testPassed($process->getExitCode(), $test))
			{
				break;
			}

            $this->command->line('retrying...');
		}

        $this->command->info($ok ? 'OK' : 'FAILED');

        $this->dataRepository->storeTestResult($test, $lines, $ok, $this->shell->startedAt, $this->shell->endedAt);

		$this->deleteTeeTempFile($test);

		return true;
	}

	public function showProgress($line)
	{
		$this->command->line($line);
	}

    private function testPassed($exitCode, $test)
    {
        if ($exitCode !== 0) {
            return false;
        }

        if (!$test->suite->tester->require_tee) {
            return true;
        }

        preg_match_all("/{$test->suite->tester->error_pattern}/", $this->getPipedFileContents(), $matches, PREG_SET_ORDER);

        return count($matches) == 0;
    }

    private function wasPiped($test)
    {
        return $this->shouldPipe($test) && file_exists($this->pipedFile);
    }
}
