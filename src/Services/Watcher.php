<?php

namespace PragmaRX\TestsWatcher\Services;

use Illuminate\Console\Command;
use JasonLewis\ResourceWatcher\Watcher as ResourceWatcher;
use PragmaRX\TestsWatcher\Data\Repositories\Data as DataRepository;

class Watcher extends Base
{
    /**
     * Is the watcher initialized?
     *
     * @var
     */
    protected $is_initialized;

    /**
     * The file watcher.
     *
     * @var \JasonLewis\ResourceWatcher\Watcher
     */
    protected $watcher;

    /**
     * Folder listeners.
     *
     * @var array
     */
    protected $listeners;

    /**
     * Console command object.
     *
     * @var
     */
    protected $command;

    /**
     * Watcher Repository.
     *
     * @var \PragmaRX\TestsWatcher\Data\Repositories\Data
     */
    private $dataRepository;

    /**
     * @var \PragmaRX\TestsWatcher\Services\Loader
     */
    private $loader;

    /**
     * Instantiate a Watcher.
     *
     * @param DataRepository  $dataRepository
     * @param ResourceWatcher $watcher
     * @param Loader          $loader
     */
    public function __construct(DataRepository $dataRepository, ResourceWatcher $watcher, Loader $loader)
    {
        $this->dataRepository = $dataRepository;

        $this->watcher = $watcher;

        $this->loader = $loader;
    }

    /**
     * Check if has changed and add test to queue.
     *
     * @param $event
     * @param $path
     *
     * @return bool
     */
    private function firedOnlyOne($event, $path)
    {
        if ($test = $this->dataRepository->isTestFile($path)) {
            if ($test->sha1Changed() && !$this->dataRepository->isEnqueued($test)) {
                $this->dataRepository->addTestToQueue($test);

                $this->showMessage($event, $path);

                $this->showProgress('Test added to queue');
            }

            return true;
        }

        return false;
    }

    /**
     * Watch for file changes.
     *
     * @param Command $command
     *
     * @return bool
     */
    public function run(Command $command)
    {
        $this->setCommand($command);

        $this->initialize();

        $this->watch();

        return true;
    }

    /**
     * Initialize the Watcher.
     */
    private function initialize()
    {
        $this->showComment($this->getConfig('names.watcher'));

        if (!$this->is_initialized) {
            $this->loader->loadEverything();

            $this->is_initialized = true;
        }
    }

    /**
     * Set the command.
     *
     * @param $command
     */
    private function setCommand($command)
    {
        $this->command = $command;

        $this->loader->setCommand($this->command);
    }

    /**
     * Display a message on terminal.
     *
     * @param $event
     * @param $path
     */
    private function showMessage($event, $path)
    {
        $this->showProgress("File {$path} was ".$this->getEventName($event->getCode()), true);
    }

    /**
     * Watch folders for changes.
     */
    private function watch()
    {
        $this->showProgress('Booting watchers...');

        foreach ($this->loader->watchFolders as $folder) {
            if (!file_exists($folder)) {
                $this->showProgress("Folder {$folder} does not exists");

                continue;
            }

            $this->showProgress('Watching '.$folder);

            $this->listeners[$folder] = $this->watcher->watch($folder);

            $this->listeners[$folder]->anything(function ($event, $resource, $path) {
                if (!$this->isExcluded($path)) {
                    $this->fireEvent($event, $resource, $path);
                }
            });
        }

        $this->watcher->start();
    }

    /**
     * Fire file modified event.
     *
     * @param $event
     * @param $resource
     * @param $path
     */
    public function fireEvent($event, $resource, $path)
    {
        if ($this->firedOnlyOne($event, $path)) {
            return;
        }

        $this->showMessage($event, $path);

        if ($this->queueTestSuites($path)) {
            return;
        }

        $this->showProgress('All tests added to queue');

        $this->dataRepository->queueAllTests();
    }

    /**
     * Check if folder is excluded.
     *
     * @param $folder
     *
     * @return bool
     */
    public function isExcluded($folder)
    {
        return $this->dataRepository->isExcluded($this->loader->exclusions, $folder);
    }

    /**
     * Queue tests for suites.
     *
     * @param $path
     *
     * @return bool tests were queued
     */
    private function queueTestSuites($path)
    {
        $queued = false;

        $suites = $this->dataRepository->getSuitesForPath($path);

        foreach ($suites as $suite) {
            $queued = true;

            $this->showProgress('Adding all tests for the '.$suite->name.' suite');

            $this->dataRepository->queueTestsForSuite($suite->id);
        }

        return $queued;
    }
}
