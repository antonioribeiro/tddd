<?php

namespace PragmaRX\Tddd\Package\Http\Controllers;

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
            view('pragmarx/tddd::dashboard')
                ->with('laravel', $this->dataRepository->getJavascriptClientData());
    }

    /**
     * Dashboard index.
     *
     * @return \Illuminate\Http\Response
     */
    public function data()
    {
        return $this->success([
            'projects' => $this->dataRepository->getProjects(),
        ]);
    }
}
