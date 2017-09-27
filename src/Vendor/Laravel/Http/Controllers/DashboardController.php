<?php

namespace PragmaRX\Ci\Vendor\Laravel\Http\Controllers;

use Response;
use PragmaRX\Ci\Support\Notifier;
use Illuminate\Routing\Controller;
use PragmaRX\Ci\Data\Repositories\Data;

class DashboardController extends Controller
{
	/**
	 * @var Data
	 */
	public $dataRepository;

	public function __construct(Data $dataRepository)
	{
		$this->dataRepository = $dataRepository;
	}

    public function index()
    {
        return view('pragmarx/ci::dashboard');
    }

	public function allTests($project_id = null)
	{
		return $this->success(['tests' => $this->dataRepository->getTests($project_id)]);
	}

	public function allProjects()
	{
		return $this->success(['projects' => $this->dataRepository->getProjects()]);
	}

	public function enableTests($enable, $project_id, $test_id = null)
	{
		$enabled = $this->dataRepository->enableTests($enable, $project_id, $test_id);

		return $this->success(['enabled' => $enabled]);
	}

    public function makeOpenFileCommand($fileName, $line)
    {
        $fileName = base64_decode($fileName);

        return
            config('ci.editor.bin') .
            (!is_null($line) ? " --line {$line}" : '') .
            " {$fileName}"
        ;
    }

    public function reset($project_id)
    {
        $this->dataRepository->reset($project_id);

        return $this->success();
    }

	public function runTest($test_id)
	{
		$this->dataRepository->runTest($test_id);

		return $this->success();
	}

	public function runAll($project_id)
	{
		$this->dataRepository->runAll($project_id);

		return $this->success();
	}

	public function success($result = [])
	{
		return Response::json(array_merge(['success' => true], $result));
	}

    public function openFile($fileName, $line = null)
    {
        shell_exec($this->makeOpenFileCommand($fileName, $line));

        return $this->success();
    }

    public function notify($type = 'failed', $count = 0, $total = 0)
    {
        Notifier::notify(
            $type == 'failed' ? 'FAILING TESTS' : 'Tests passing',
            $type == 'failed' ? "At least {$count} of {$total} are failing" : 'All your tests are passing, congrats!'
        );

        return $this->success();
    }
}
