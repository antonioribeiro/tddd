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
}
