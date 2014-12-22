<?php

namespace PragmaRX\Ci\Vendor\Laravel\Http\Controllers;

use Illuminate\Routing\Controller;
use PragmaRX\Ci\Data\Repositories\Data;
use Response;

class DashboardController extends Controller {

	/**
	 * @var Data
	 */
	private $dataRepository;

	public function __construct(Data $dataRepository)
	{
		$this->dataRepository = $dataRepository;
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

	private function success($result = [])
	{
		return Response::json(array_merge(['success' => true], $result));
	}

}
