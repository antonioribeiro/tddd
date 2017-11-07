<?php

namespace PragmaRX\TestsWatcher\Package\Services;

use PragmaRX\TestsWatcher\Package\Facades\Config;
use PragmaRX\TestsWatcher\Package\Data\Repositories\Data as DataRepository;

class Loader extends Base
{
    /**
     * Exclude folders.
     *
     * @var
     */
    public $exclusions;

    /**
     * Data repository.
     *
     * @var
     */
    public $dataRepository;

    /**
     * Folders to be watched.
     *
     * @var
     */
    public $watchFolders;

    /**
     * Instantiate a Watcher.
     *
     * @param \PragmaRX\TestsWatcher\Package\Data\Repositories\Data $dataRepository
     */
    public function __construct(DataRepository $dataRepository)
    {
        $this->dataRepository = $dataRepository;
    }

    /**
     * Create or update the suite.
     *
     * @param $suite_name
     * @param $project
     * @param $suite_data
     */
    private function createSuite($suite_name, $project, $suite_data)
    {
        $this->showProgress("  -- suite '{$suite_name}'");

        if (!$this->dataRepository->createOrUpdateSuite($suite_name, $project->id, $suite_data)) {
            $this->displayMessages($this->dataRepository->getMessages());
            die;
        }
    }

    /**
     * Read configuration and load testers, projects, suites...
     */
    public function loadEverything()
    {
        $this->showProgress('Config loaded from ' . Config::getConfigPath());

        $this->loadTesters();

        $this->loadProjects();

        $this->loadTests();
    }

    /**
     * Load all testers to database.
     */
    public function loadTesters()
    {
        $this->showProgress('Loading testers...', 'info');

        if (!is_arrayable($testers = $this->config('testers')) or count($testers) == 0) {
            $this->showProgress('No testers found.', 'error');

            return;
        }

        foreach ($testers as $data) {
            $this->showProgress("TESTER: {$data['name']}");

            $this->dataRepository->createOrUpdateTester($data);
        }

        $this->dataRepository->deleteMissingTesters(array_keys($testers));
    }

    /**
     * Load all projects to database.
     */
    public function loadProjects()
    {
        $this->showProgress('Loading projects and suites...', 'info');

        if (!is_arrayable($projects = $this->config('projects')) or count($projects) == 0) {
            $this->showProgress('No projects found.', 'error');

            return;
        }

        foreach ($projects as $data) {
            $this->showProgress("Project '{$data['name']}'", 'comment');

            $project = $this->dataRepository->createOrUpdateProject($data['name'], $data['path'], $data['tests_path']);

            $this->refreshProjectSuites($data, $project);

            $this->addToWatchFolders($data['path'], $data['watch_folders']);

            $this->addToExclusions($data['path'], $data['exclude']);
        }

        $this->dataRepository->deleteMissingProjects(collect($this->config('projects'))->pluck('name')->toArray());
    }

    /**
     * Load all test files to database.
     */
    public function loadTests()
    {
        $this->showProgress('Loading tests...', 'info');

        $this->dataRepository->syncTests($this->exclusions);
    }

    /**
     * Add folders to the watch list.
     *
     * @param $path
     * @param $watch_folders
     */
    public function addToWatchFolders($path, $watch_folders)
    {
        collect($watch_folders)->each(function ($folder) use ($path) {
            $this->watchFolders[] = !file_exists($new = make_path([$path, $folder])) && file_exists($folder)
                ? $folder
                : $new;
        });
    }

    /**
     * Add path to exclusions list.
     *
     * @param $path
     * @param $exclude
     */
    public function addToExclusions($path, $exclude)
    {
        collect($exclude)->each(function ($folder) use ($path) {
            $this->exclusions[] = $excluded = make_path([$path, $folder]);

            $this->showProgress("EXCLUDED: {$excluded}");
        });
    }

    /**
     * Refresh all suites for a project.
     *
     * @param $data
     * @param $project
     */
    private function refreshProjectSuites($data, $project)
    {
        $this->dataRepository->removeMissingSuites($suites = $data['suites'], $project);

        collect($suites)->map(function ($data, $name) use ($project) {
            $this->createSuite($name, $project, $data);
        });
    }
}
