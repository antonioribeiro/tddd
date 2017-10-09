<?php

namespace PragmaRX\TestsWatcher\Vendor\Laravel\Http\Controllers;

use Response;
use Illuminate\Routing\Controller;
use PragmaRX\TestsWatcher\Data\Repositories\Data;

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

    private function addProjectRootPath($fileName, $project)
    {
        if (starts_with($fileName, DIRECTORY_SEPARATOR) || empty($project)) {
            return $fileName;
        }

        return $project->path . DIRECTORY_SEPARATOR . $fileName;
    }

    public function index()
    {
        return
            view('pragmarx/ci::dashboard')
                ->with('laravel', $this->dataRepository->getJavascriptClientData())
        ;
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

    public function makeOpenFileCommand($fileName, $line, $project_id)
    {
        $fileName = $this->addProjectRootPath(
            base64_decode($fileName),
            $this->dataRepository->findProjectById($project_id)
        );

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

    public function openFile($fileName, $line = null, $project_id = null)
    {
        shell_exec($this->makeOpenFileCommand($fileName, $line, $project_id));

        return $this->success();
    }

    public function notify($project_id)
    {
        $this->dataRepository->notify($project_id);

        return $this->success();
    }

    public function imageDownload($filename)
    {
        return response()->download(
            base64_decode($filename)
        );
    }
}
