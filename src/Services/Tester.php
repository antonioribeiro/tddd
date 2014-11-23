<?php

namespace PragmaRX\Ci\Services;

use PragmaRX\Ci\Data\Repositories\Data as DataRepository;
use PragmaRX\Ci\Support\ShellExec;
use Illuminate\Console\Command;
use Config;

class Tester {

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

	/**
	 * Instantiate a Tester.
	 *
	 * @param DataRepository $dataRepository
	 */
	public function __construct(DataRepository $dataRepository, ShellExec $shell)
	{
		$this->dataRepository = $dataRepository;

		$this->shell = $shell;
	}

	/**
	 * Run the tester.
	 *
	 * @param Command $command
	 */
	public function run(Command $command)
	{
		$this->command = $command;

		$this->command->comment('Laravel-CI - Tester');

		$this->startTester();
	}

	/**
	 * Start the timed tester.
	 *
	 * @param int $interval
	 * @param null $timeout
	 * @param Closure $callback
	 */
	public function startTester($interval = 1000000, $timeout = null, Closure $callback = null)
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
				$this->stopTest();
			}
		}
	}

	/**
	 * Stop testing.
	 *
	 * @return void
	 */
	public function stopTest()
	{
		$this->testing = false;
	}

	/**
	 * Find and execute a test.
	 *
	 */
	private function test()
	{
		$me = $this;

		if ( ! $test = $this->dataRepository->getNextTestFromQueue())
		{
			return false;
		}

		$executeCommand = 'Executing '.$test->testCommand;

		$this->dataRepository->markTestAsRunning($test);

		$this->command->drawLine($executeCommand);

		$this->command->line($executeCommand);

		foreach(range(0, $test->suite->retries-1) as $item)
		{
			$process = $this->shell->exec($test->testCommand, $test->suite->project->path, function($type, $buffer) use ($me)
			{
				if (Config::get('pragmarx/ci::show_progress'))
				{
					$me->showProgress($buffer);
				}
			});

			$lines = $process->getOutput();

			if ($ok = ($process->getExitCode() === 0))
			{
				break;
			}

			$this->command->line('retrying...');
		}

		if ($this->dataRepository->storeTestResult($test, $lines, $ok))
		{
			$this->command->info('OK');
		}
		else
		{
			$this->command->error('FAILED');
		}

		return true;
	}

	public function showProgress($line)
	{
		$this->command->line($line);
	}

}
