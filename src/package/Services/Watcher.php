<?php

namespace PragmaRX\TestsWatcher\Package\Services;

use Illuminate\Console\Command;
use JasonLewis\ResourceWatcher\Watcher as ResourceWatcher;
use PragmaRX\TestsWatcher\Package\Data\Repositories\Data as DataRepository;

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
     * Watcher Repository.
     *
     * @var \PragmaRX\TestsWatcher\Package\Data\Repositories\Data
     */
    private $dataRepository;

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

        parent::__construct();
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

                $this->showEventMessage($event, $path);

                $this->showProgress('QUEUE: test added to queue');
            }

            return true;
        }

        return false;
    }

    /**
     * Check if the changed file is the config file and reload.
     *
     * @param $path
     *
     * @return bool
     */
    private function isConfig($path)
    {
        if ($path == $this->config->get('config_file') && file_exists($path)) {
            $this->config->set(require $path);

            $this->loader->loadEverything();

            return true;
        }

        return false;
    }

    /**
     * Watch for file changes.
     *
     * @param Command $command
     * @param bool    $showTests
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
        $this->showComment($this->config->get('names.watcher'), 'info');

        if (!$this->is_initialized) {
            $this->loader->loadEverything();

            $this->is_initialized = true;
        }
    }

    /**
     * Display a message about the event on terminal.
     *
     * @param $event
     * @param $path
     */
    private function showEventMessage($event, $path)
    {
        $this->showProgress("FILE CHANGED: {$path} was ".$this->getEventName($event->getCode()), 'error');
    }

    /**
     * Watch folders for changes.
     */
    private function watch()
    {
        $this->showProgress('BOOT: booting watchers...');

        foreach ($this->loader->watchFolders as $folder) {
            if (!file_exists($folder)) {
                $this->showProgress("ERROR: folder {$folder} does not exists", 'error');

                continue;
            }

            $this->showProgress('WATCHING '.$folder);

            $this->listeners[$folder] = $this->watcher->watch($folder);

            $this->listeners[$folder]->anything(function ($event, $resource, $path) {
                if (!$this->isExcluded($path)) {
                    $this->fireEvent($event, $resource, $path);
                }
            });
        }

        $this->watchConfigFile();

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
        if ($this->isConfig($path)) {
            return;
        }

        if ($this->firedOnlyOne($event, $path)) {
            return;
        }

        $this->loader->loadEverything();

        $this->showEventMessage($event, $path);

        if ($this->queueTestSuites($path)) {
            return;
        }

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

            $this->showProgress('QUEUE: adding all tests for the '.$suite->name.' suite');

            $this->dataRepository->queueTestsForSuite($suite->id);
        }

        return $queued;
    }

    /**
     * Watch the config file for changes.
     */
    private function watchConfigFile()
    {
        if (file_exists($file = $this->config->get('config_file'))) {
            $this->watcher->watch($file);

            $this->showProgress("WATCHING CONFIG: {$file}");
        }
    }
}
