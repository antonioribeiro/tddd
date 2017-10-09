<?php

namespace PragmaRX\TestsWatcher\Vendor\Laravel\Http\Controllers;

use Illuminate\Routing\Controller;
use PragmaRX\TestsWatcher\Data\Repositories\Data;
use PragmaRX\TestsWatcher\Support\Executor;
use Response;

class DashboardController extends Controller
{
    /**
     * @var Data
     */
    public $dataRepository;

    /**
     * @var \PragmaRX\TestsWatcher\Support\Executor
     */
    private $executor;

    /**
     * DashboardController constructor.
     *
     * @param Data $dataRepository
     * @param \PragmaRX\TestsWatcher\Support\Executor $executor
     */
    public function __construct(Data $dataRepository, Executor $executor)
    {
        $this->dataRepository = $dataRepository;

        $this->executor = $executor;
    }

    /**
     * Dashboard index.
     *
     * @return $this
     */
    public function index()
    {
        return
            view('pragmarx/ci::dashboard')
                ->with('laravel', $this->dataRepository->getJavascriptClientData());
    }

    /**
     * Get all tests.
     *
     * @param null $project_id
     * @return mixed
     */
    public function allTests($project_id = null)
    {
        return $this->success(['tests' => $this->dataRepository->getTests($project_id)]);
    }

    /**
     * Get all projects.
     *
     * @return mixed
     */
    public function allProjects()
    {
        return $this->success(['projects' => $this->dataRepository->getProjects()]);
    }

    /**
     * Enable tests.
     *
     * @param $enable
     * @param $project_id
     * @param null $test_id
     * @return mixed
     */
    public function enableTests($enable, $project_id, $test_id = null)
    {
        $enabled = $this->dataRepository->enableTests($enable, $project_id, $test_id);

        return $this->success(['enabled' => $enabled]);
    }

    /**
     * Reset all tests.
     *
     * @param $project_id
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
     * @return mixed
     */
    public function runTest($test_id)
    {
        $this->dataRepository->runTest($test_id);

        return $this->success();
    }

    /**
     * Run all tests.
     *
     * @param $project_id
     * @return mixed
     */
    public function runAll($project_id)
    {
        $this->dataRepository->runAll($project_id);

        return $this->success();
    }

    /**
     * Return a success response.
     *
     * @param array $result
     * @return mixed
     */
    public function success($result = [])
    {
        return Response::json(array_merge(['success' => true], $result));
    }

    /**
     * Open a file in the editor.
     *
     * @param $fileName
     * @param null $line
     * @param null $project_id
     * @return mixed
     */
    public function openFile($fileName, $line = null, $project_id = null)
    {
        $this->executor->shellExec(
            $this->dataRepository->makeOpenFileCommand($fileName, $line, $project_id)
        );

        return $this->success();
    }

    /**
     * Notify users.
     *
     * @param $project_id
     * @return mixed
     */
    public function notify($project_id)
    {
        $this->dataRepository->notify($project_id);

        return $this->success();
    }

    /**
     * Download an image.
     *
     * @param $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function imageDownload($filename)
    {
        return response()->download(
            base64_decode($filename)
        );
    }
}
