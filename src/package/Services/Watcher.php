<?php

namespace PragmaRX\TestsWatcher\Package\Services;

use Carbon\Carbon;
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
    protected $dataRepository;

    /**
     * The event cache.
     *
     * @var array
     */
    protected $eventCache = [];

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
     * Check if the event has expired.
     *
     * @param $event
     *
     * @return bool
     */
    protected function eventExpired($event)
    {
        $cachedDiff = $this->getCachedEvent($event)->diffInSeconds(Carbon::now());

        return $cachedDiff < $this->config('root.cache.event_timeout', 10);
    }

    /**
     * Get an event from cache.
     *
     * @param $event
     *
     * @return mixed|static
     */
    protected function getCachedEvent($event)
    {
        $path = $event->getResource()->getPath();

        return isset($this->eventCache[$path])
            ? $this->eventCache[$path]
            : $this->eventCache[$path] = Carbon::now()->subDay();
    }

    /**
     * Check if the event was processed recently.
     *
     * @param \JasonLewis\ResourceWatcher\Event $event
     *
     * @return bool
     */
    protected function eventWasProcessed($event)
    {
        if ($this->eventExpired($event)) {
            return true;
        }

        $this->resetEventCache($event);

        return false;
    }

    /**
     * Check if has changed and add test to queue.
     *
     * @param $event
     * @param $path
     *
     * @return bool
     */
    protected function firedOnlyOne($event, $path)
    {
        if ($test = $this->dataRepository->isTestFile($path)) {
            if ($test->sha1Changed() && !$this->dataRepository->isEnqueued($test)) {
                $this->dataRepository->addTestToQueue($test);

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
    protected function isConfig($path)
    {
        if ($this->config()->isConfigFile($path)) {
            $this->config()->invalidateConfig();

            $this->loader->loadEverything();

            return true;
        }

        return false;
    }

    /**
     * Reset the cache entry for an event.
     *
     * @param $event
     */
    private function resetEventCache($event)
    {
        $this->eventCache[$event->getResource()->getPath()] = Carbon::now();
    }

    /**
     * Watch for file changes.
     *
     * @param Command $command
     * @param bool    $showTests
     *
     * @param bool $showTests
     * @return bool
     */
    public function run(Command $command, $showTests = false)
    {
        $this->setCommand($command);

        $this->initialize($showTests);

        $this->watch();

        return true;
    }

    /**
     * Initialize the Watcher.
     */
    protected function initialize($showTests)
    {
        $this->showComment($this->config('root.names.watcher'), 'info');

        if (!$this->is_initialized) {
            $this->loader->loadEverything($showTests);

            $this->is_initialized = true;
        }
    }

    /**
     * Display a message about the event on terminal.
     *
     * @param $event
     * @param $path
     */
    protected function showEventMessage($event, $path)
    {
        $type = $this->config()->isConfigFile($path)
            ? 'CONFIGURATION'
            : 'FILE';

        $change = strtoupper($this->getEventName($event->getCode()));

        $this->showProgress("{$type} {$change}: {$path}", 'error');
    }

    /**
     * Watch folders for changes.
     */
    protected function watch()
    {
        $this->showProgress('BOOT: booting watchers...');

        if (is_null($this->loader->watchFolders)) {
            $this->showProgress('No watch folders found.', 'error');

            return;
        }

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
        if ($this->eventWasProcessed($event, $resource, $path)) {
            return;
        }

        $this->showProgress('event 1');
        $this->showEventMessage($event, $path);

        $this->showProgress('event 2');
        if ($this->isConfig($path)) {
            return;
        }

        $this->showProgress('event 3');

        if ($this->firedOnlyOne($event, $path)) {
            return;
        }

        $this->showProgress('event 4');
        $this->loader->loadEverything();

        $this->showProgress('event 5');
        if ($this->queueTestSuites($path)) {
            return;
        }

        $this->showProgress('event 6');
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
    protected function queueTestSuites($path)
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
    protected function watchConfigFile()
    {
        $this->showProgress('WATCHING CONFIG FILES');

        $this->config()->getConfigFiles()->each(function ($file) {
            $this->watcher->watch($file);
        });
    }
}
