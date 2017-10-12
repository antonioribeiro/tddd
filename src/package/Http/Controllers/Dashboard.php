<?php

namespace PragmaRX\TestsWatcher\Package\Http\Controllers;

class Dashboard extends Controller
{
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
     * Dashboard index.
     *
     * @param null $project_id
     *
     * @return \Illuminate\Http\Response
     */
    public function data($project_id = null)
    {
        return $this->success([
            'projects' => $this->dataRepository->getProjects(),

            'tests' => $project_id ? $this->dataRepository->getProjectTests($project_id) : [],
        ]);
    }
}
