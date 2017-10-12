<?php

namespace PragmaRX\TestsWatcher\Package\Http\Controllers;

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
}
