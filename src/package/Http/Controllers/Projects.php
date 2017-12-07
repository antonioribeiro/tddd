<?php

namespace PragmaRX\Tddd\Package\Http\Controllers;

use Illuminate\Http\Request;

class Projects extends Controller
{
    /**
     * Enable tests.
     *
     * @param $enable
     * @param $project_id
     *
     * @return mixed
     */
    public function enable($project_id, $enable)
    {
        $enabled = $this->dataRepository->enableProjects($enable, $project_id);

        return $this->success(['enabled' => $enabled]);
    }

    /**
     * Notify users.
     *
     * @param $project_id
     *
     * @return mixed
     */
    public function notify($project_id)
    {
        $this->dataRepository->notify($project_id);

        return $this->success();
    }

    /**
     * Run project tests.
     *
     * @return mixed
     */
    public function run(Request $request)
    {
        $this->dataRepository->runProjectTests($request->get('projects'));

        return $this->success();
    }

    /**
     * Reset projects tests states.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function reset(Request $request)
    {
        $this->dataRepository->reset($request->get('projects'));

        return $this->success();
    }
}
