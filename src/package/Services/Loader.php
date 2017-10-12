<?php

namespace PragmaRX\TestsWatcher\Package\Services;

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
        }
    }

    /**
     * Read configuration and load testers, projects, suites...
     */
    public function loadEverything()
    {
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

        foreach ($this->getConfig('testers') as $name => $data) {
            $this->dataRepository->createOrUpdateTester($name, $data);
        }

        $this->dataRepository->deleteUnavailableTesters(array_keys($this->getConfig('testers')));
    }

    /**
     * Load all projects to database.
     */
    public function loadProjects()
    {
        $this->showProgress('Loading projects and suites...');

        $this->dataRepository->clearSuites();

        foreach ($this->getConfig('projects') as $name => $data) {
            $this->showProgress("Project '{$name}'", 'comment');

            $project = $this->dataRepository->createOrUpdateProject($name, $data['path'], $data['tests_path']);

            foreach ($data['suites'] as $suite_name => $suite_data) {
                $this->createSuite($suite_name, $project, $suite_data);
            }

            $this->addToWatchFolders($data['path'], $data['watch_folders']);

            $this->addToExclusions($data['path'], $data['exclude']);
        }

        $this->dataRepository->deleteUnavailableProjects(array_keys($this->getConfig('projects')));
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
        foreach ($watch_folders as $folder) {
            $this->watchFolders[] = !file_exists($new = make_path([$path, $folder])) && file_exists($folder)
                ? $folder
                : $new;
        }
    }

    /**
     * Add path to exclusions list.
     *
     * @param $path
     * @param $exclude
     */
    public function addToExclusions($path, $exclude)
    {
        foreach ($exclude as $folder) {
            $this->exclusions[] = $excluded = make_path([$path, $folder]);

            $this->showProgress("EXCLUDED: {$excluded}");
        }
    }
}
