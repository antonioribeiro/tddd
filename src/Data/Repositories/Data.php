<?php

namespace PragmaRX\Ci\Data\Repositories;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use PragmaRX\Ci\Vendor\Laravel\Entities\Run;
use PragmaRX\Ci\Vendor\Laravel\Entities\Test;
use PragmaRX\Ci\Vendor\Laravel\Entities\Suite;
use PragmaRX\Ci\Vendor\Laravel\Entities\Queue;
use PragmaRX\Ci\Vendor\Laravel\Entities\Tester;
use PragmaRX\Ci\Vendor\Laravel\Entities\Project;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class Data {

	/**
	 * Internal constants.
	 *
	 */
	const STATE_IDLE = 'idle';

	const STATE_QUEUED = 'queued';

	const STATE_OK = 'ok';

	const STATE_FAILED = 'failed';

	const STATE_RUNNING = 'running';

	/**
	 * Create or update a tester.
	 *
	 * @param $name
	 * @param $data
	 */
	public function createOrUpdateTester($name, $data)
	{
		Tester::updateOrCreate(
			['name' => $name],
			[
				'command' => $data['command'],
				'output_folder' => isset($data['output_folder']) ? $data['output_folder'] : null,
				'output_html_fail_extension' => isset($data['output_html_fail_extension']) ? $data['output_html_fail_extension'] : null,
				'output_png_fail_extension' => isset($data['output_png_fail_extension']) ? $data['output_png_fail_extension'] : null,
			]
		);
	}

	/**
	 * Create or update a project.
	 *
	 * @param $name
	 * @param $path
	 * @param $tests_path
	 * @return static
	 */
	public function createOrUpdateProject($name, $path, $tests_path)
	{
		return Project::updateOrCreate(['name' => $name], ['path' => $path, 'tests_path' => $tests_path]);
	}

	/**
	 * Create or update a suite.
	 *
	 * @param $name
	 * @param $project_id
	 * @param $suite_data
	 * @return static
	 */
	public function createOrUpdateSuite($name, $project_id, $suite_data)
	{
		$tester = Tester::where('name', $suite_data['tester'])->first();

		return Suite::updateOrCreate(
			['name' => $name, 'project_id' => $project_id],
			[
				'tester_id' => $tester->id,
			    'tests_path' => $suite_data['tests_path'],
			    'command_options' => $suite_data['command_options'],
			    'file_mask' => $suite_data['file_mask'],
			    'retries' => $suite_data['retries'],
			]
		);
	}

	/**
	 * Get all suites.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function getSuites()
	{
		return Suite::all();
	}

	/**
	 * Create or update a test.
	 *
	 * @param $file
	 * @param $suite
	 */
	public function createOrUpdateTest($file, $suite)
	{
		$exists = Test::where('name', $file->getRelativePathname())
					->where('suite_id', $suite->id)
					->first();

		$test = Test::updateOrCreate(
			[
	            'name' => $file->getRelativePathname(),
	            'suite_id' => $suite->id,
			]
		);

		if ( ! $exists)
		{
			$this->addTestToQueue($test);
		}
	}

	/**
	 * Sync all tests.
	 *
	 * @param $exclusions
	 */
	public function syncTests($exclusions)
	{
		foreach($this->getSuites() as $suite)
		{
			$this->syncTestsForSuite($suite, $exclusions);
		}
	}

	/**
	 * Sync all tests for a particular suite.
	 *
	 * @param $suite
	 * @param $exclusions
	 */
	private function syncTestsForSuite($suite, $exclusions)
	{
		$files = $this->getAllFilesFromSuite($suite);

		foreach($files as $file)
		{
			if ( ! $this->isExcluded($exclusions, null, $file))
			{
				$this->createOrUpdateTest($file, $suite);
			}
			else
			{
				// If the test already exists, delete it.
				//
				if ($test = $this->findTestByNameAndSuite($file, $suite))
				{
					$test->delete();
				}
			}
		}

		foreach($suite->tests as $test)
		{
			if ( ! file_exists($path = make_path([$suite->testsFullPath, $test->name])))
			{
				$test->delete();
			}
		}
	}

	/**
	 * Get all files from a suite.
	 *
	 * @param $suite
	 * @return array
	 */
	private function getAllFilesFromSuite($suite)
	{
		$files = Finder::create()->files()->in($suite->testsFullPath);

		if ($suite->file_mask)
		{
			$files->name($suite->file_mask);
		}

		return iterator_to_array($files, false);
	}

	/**
	 * Check if a file is a test file.
	 *
	 * @param $path
	 * @return \___PHPSTORM_HELPERS\static|bool|mixed
	 */
	public function isTestFile($path)
	{
		foreach(Test::all() as $test)
		{
			if ($test->fullPath == $path)
			{
				return $test;
			}
		}

		return false;
	}

	/**
	 * Queue all tests.
	 *
	 */
	public function queueAllTests()
	{
		foreach(Test::all() as $test)
		{
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

		foreach($tests as $test)
		{
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
		if ($test->enabled && ! $this->isEnqueued($test))
		{
			Queue::updateOrCreate(['test_id' => $test->id]);

			if ( $force || ! in_array($test->state, [self::STATE_RUNNING, self::STATE_QUEUED]))
			{
				$test->state = self::STATE_QUEUED;
				$test->timestamps = false;
				$test->save();
			}
		}
	}

	/**
	 * Get a test from the queue.
	 *
	 */
	public function getNextTestFromQueue()
	{
		$query = Queue::join('tests', 'tests.id', '=', 'queue.test_id')
						->where('tests.enabled', true);

		if ( ! $queue = $query->first() )
		{
			return;
		}

		return $queue->test;
	}

	/**
	 * Store the test result.
	 *
	 * @param $test
	 * @param $lines
	 * @param $ok
	 * @return mixed
	 */
	public function storeTestResult($test, $lines, $ok)
	{
		$run = Run::create([
	        'test_id' => $test->id,
	        'was_ok' => $ok,
	        'log' => $lines ?: '(empty)',
		    'html' => $this->getOutput($test, $test->suite->tester->output_folder, $test->suite->tester->output_html_fail_extension),
		    'png' => $this->getOutput($test, $test->suite->tester->output_folder, $test->suite->tester->output_png_fail_extension),
		]);

		$test->state = $ok ? self::STATE_OK : self::STATE_FAILED;
		$test->last_run_id = $run->id;
		$test->save();

		$this->removeTestFromQueue($test);

		return $ok;
	}

	/**
	 * Remove test from que run queue.
	 *
	 * @param $test
	 * @return mixed
	 */
	private function removeTestFromQueue($test)
	{
		Queue::where('test_id', $test->id)->delete();

		return $test;
	}

	/**
	 * Mark a test as being running.
	 *
	 * @param $test
	 */
	public function markTestAsRunning($test)
	{
		$test->state = self::STATE_RUNNING;

		$test->save();
	}

	/**
	 * Delete unavailable testers.
	 *
	 * @param $testers
	 */
	public function deleteUnavailableTesters($testers)
	{
		foreach(Tester::all() as $tester)
		{
			if ( ! in_array($tester->name, $testers))
			{
				$tester->delete();
			}
		}
	}

	/**
	 * Delete unavailable projects.
	 *
	 * @param $projects
	 */
	public function deleteUnavailableProjects($projects)
	{
		foreach(Project::all() as $project)
		{
			if ( ! in_array($project->name, $projects))
			{
				$project->delete();
			}
		}
	}

	/**
	 * Check if a path is excluded.
	 *
	 * @param $exclusions
	 * @param $path
	 * @param string $file
	 * @return bool
	 */
	public function isExcluded($exclusions, $path, $file = '')
	{
		if ($file)
		{
			if ( ! $file instanceof SplFileInfo)
			{
				$path = make_path([$path, $file]);
			}
			else
			{
				$path = $file->getPathname();
			}
		}

		foreach($exclusions ?: [] as $excluded)
		{
			if (starts_with($path, $excluded))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Find a test by name and suite.
	 *
	 * @param $suite
	 * @param $file
	 * @return mixed
	 */
	private function findTestByNameAndSuite($file, $suite)
	{
		return Test::where('name', $file->getRelativePathname())->where('suite_id', $suite->id)->first();
	}

	/**
	 * Get all tests.
	 *
	 * @param null $project_id
	 * @return array
	 */
	public function getTests($project_id = null)
	{
		$tests = [];

		$order = "(case
						when state = 'running' then 1
						when state = 'failed' then 2
						when state = 'queued' then 3
						when state = 'ok' then 4
						when state = 'idle' then 5
			        end) asc,

			        updated_at desc";

		$query = Test::select('tests.*')
					->join('suites', 'suites.id', '=', 'suite_id')
					->orderByRaw($order);

		if ($project_id)
		{
			$query->where('project_id', $project_id);
		}

		foreach ($query->get() as $test)
		{
			if ($log = Run::where('test_id', $test->id)->orderBy('created_at', 'desc')->first())
			{
				$html = $log->html;

				$image = $log->png;

				$log = $this->formatLog($log);
			}

			$tests[] = [
				'id' => $test->id,
			    'name' => $test->name,
			    'updated_at' => $test->updated_at->diffForHumans(),
			    'state' => $test->state,
			    'log' => $log,
			    'html' => isset($html) ? $html : null,
			    'image' => isset($image) ? $image : null,
			    'enabled' => $test->enabled,
			];
		}

		return $tests;
	}

	/**
	 * Get all projects.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function getProjects()
	{
		return Project::all();
	}

	/**
	 * Format output log.
	 *
	 * @param $log
	 * @return mixed|string
	 */
	private function formatLog($log)
	{
		if ($log)
		{
			$log = $this->ansi2Html($log->log);
		}

		return $log;
	}

	/**
	 * Convert output ansi chars to html.
	 *
	 * @param $log
	 * @return mixed|string
	 */
	private function ansi2Html($log)
	{
		$converter = new AnsiToHtmlConverter();

		$log = $converter->convert($log);

		$log = str_replace(chr(13).chr(10), '<br>', $log);

		$log = str_replace(chr(10), '<br>', $log);

		$log = str_replace(chr(13), '<br>', $log);

		return $log;
	}

	/**
	 * Enable tests.
	 *
	 * @param $enable
	 * @param $project_id
	 * @param null $test_id
	 * @return bool
	 */
	public function enableTests($enable, $project_id, $test_id = null)
	{
		$enable = $enable === 'true';

		$tests = $this->queryTests($project_id, $test_id)->get();

		foreach($tests as $test)
		{
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
		if ( ! $test instanceof Test)
		{
			$test = Test::find($test);
		}

		$this->addTestToQueue($test, $force);
	}

	/**
	 * Enable a test.
	 *
	 * @param $enable
	 * @param $test
	 */
	private function enableTest($enable, $test)
	{
		$test->timestamps = false;

		$test->enabled = $enable;

		$test->save();

		if ( ! $enable)
		{
			return $this->removeTestFromQueue($test);
		}

		if ($test->state !== self::STATE_OK)
		{
			$this->addTestToQueue($test);
		}
	}

	/**
	 * Get the test output.
	 *
	 * @param $test
	 * @param $outputFolder
	 * @param $extension
	 * @return bool|mixed|null|string
	 */
	private function getOutput($test, $outputFolder, $extension)
	{
		if ( ! $outputFolder)
		{
			return null;
		}

		$name = str_replace(['.php', '::', '\\', '/'],['', '.', '', ''], $test->name);

		$path = make_path([$test->suite->project->path, $outputFolder]);

		if (file_exists($file = make_path([$path, $name . $extension])))
		{
			return $this->encodeFile($file);
		}

		return null;
	}

	/**
	 * Encode a image or html for database storage.
	 *
	 * @param $file
	 * @return bool|mixed|string
	 */
	private function encodeFile($file)
	{
		$type = pathinfo($file, PATHINFO_EXTENSION);

		$data = file_get_contents($file);

		if ($type == 'html')
		{
			return $data;
		}

		return 'data:image/' . $type . ';base64,' . base64_encode($data);
	}

	/**
	 * Is the test in the queue?
	 *
	 * @param $test
	 * @return bool
	 */
	private function isEnqueued($test)
	{
		return
			$test->state == self::STATE_QUEUED
			&&
			Queue::where('test_id', $test->id)->first();
	}

	/**
	 * Get all suites for a path.
	 *
	 * @param $path
	 * @return mixed
	 */
	public function getSuitesForPath($path)
	{
		$projects = $this->getProjects();

		// Reduce the collection of projects by those whose path properties
		// (should be only 1) are contained in the fullpath of our
		// changed file
		$filtered_projects = $projects->filter(function ($project) use ($path)
		{
			return substr_count($path, $project->path) > 0;
		});

		// at this point we have (hopefully only 1) project. Now we need
		// the suite(s) associated with the project.
		return Suite::whereIn('project_id', $filtered_projects->lists('id'))
				->get();
	}

	/**
	 * Run all tests or projects tests.
	 *
	 * @param null $project_id
	 */
	public function runAll($project_id = null)
	{
		$tests = $this->queryTests($project_id)->get();

		foreach($tests as $test)
		{
			$this->enableTest(true, $test);

			// Force test to the queue
			$this->runTest($test, true);
		}
	}

	/**
	 * Query tests.
	 *
	 * @param $project_id
	 * @param $test_id
	 * @return mixed
	 */
	private function queryTests($project_id = null, $test_id = null)
	{
		$query = Test::select('tests.*')
					->join('suites', 'suites.id', '=', 'tests.suite_id');

		if ($project_id)
		{
			$query->where('suites.project_id', $project_id);
		}

		if ($test_id)
		{
			$query->where('tests.id', $test_id);
		}

		return $query;
	}

}
