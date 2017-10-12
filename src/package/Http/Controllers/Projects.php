<?php

namespace PragmaRX\TestsWatcher\Package\Http\Controllers;

class Projects extends Controller
{
    /**
     * Get all projects.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->success(['projects' => $this->dataRepository->getProjects()]);
    }

    /**
     * Enable tests.
     *
     * @param $enable
     * @param $project_id
     *
     * @return mixed
     */
    public function enable($enable, $project_id)
    {
        $enabled = $this->dataRepository->enableProjects($enable, $project_id);

        return $this->success(['enabled' => $enabled]);
    }

    /**
     * Get all tests.
     *
     * @param null $project_id
     *
     * @return mixed
     */
    public function tests($project_id = null)
    {
        return $this->success(['tests' => $this->dataRepository->getTests($project_id)]);
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
}
