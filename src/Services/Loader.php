<?php

namespace PragmaRX\TestsWatcher\Services;

use PragmaRX\TestsWatcher\Data\Repositories\Data as DataRepository;

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
     * Command.
     *
     * @var
     */
    protected $command;

    /**
     * Folders to be watched.
     *
     * @var
     */
    public $watchFolders;

    /**
     * Instantiate a Watcher.
     *
     * @param DataRepository $dataRepository
     */
    public function __construct(DataRepository $dataRepository)
    {
        $this->dataRepository = $dataRepository;
    }

    /**
     * Read configuration and load testers, projects, suites...
     */
    public function loadEverything()
    {
        $this->command->info('Loading testers...');
        $this->loadTesters();

        $this->command->info('Loading projects and suites...');
        $this->loadProjects();

        $this->command->info('Loading tests...');
        $this->loadTests();
    }

    /**
     * Load all testers to database.
     */
    public function loadTesters()
    {
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
        $this->dataRepository->clearSuites();

        foreach ($this->getConfig('projects') as $name => $data) {
            $this->command->line("Project '{$name}'");

            $project = $this->dataRepository->createOrUpdateProject($name, $data['path'], $data['tests_path']);

            foreach ($data['suites'] as $suite_name => $suite_data) {
                $this->command->line("  -- suite '{$suite_name}'");

                $this->dataRepository->createOrUpdateSuite($suite_name, $project->id, $suite_data);
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
            $this->exclusions[] = make_path([$path, $folder]);
        }
    }

    /**
     * Set the command.
     *
     * @param mixed $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }
}
