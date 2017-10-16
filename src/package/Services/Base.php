<?php

namespace PragmaRX\TestsWatcher\Package\Services;

use JasonLewis\ResourceWatcher\Event;

class Base
{
    /**
     * The command object.
     *
     * @object \PragmaRX\TestsWatcher\Package\Console\Commands\BaseCommand
     */
    protected $command;

    /**
     * @var \PragmaRX\TestsWatcher\Package\Services\Loader
     */
    protected $loader;

    /**
     * @var array
     */
    protected $config;


    /**
     * Instantiate a Watcher.
     *
     */
    public function __construct()
    {
        $this->config = app('ci.config');
    }

    /**
     * Get a configuration key.
     *
     * @param $key
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function config($key)
    {
        $this->loadConfig();

        if (is_null($value = array_get($this->config, $key))) {
            throw new \Exception("The configuration key '{$key}' was not defined.");
        }

        return $value;
    }

    private function loadConfig()
    {
        if (is_null($this->config)) {
            $this->config = config('ci');
        }
    }

    /**
     * Set the config.
     *
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Show progress in terminal.
     *
     * @param $line
     */
    public function showProgress($line, $type = 'line')
    {
        $this->command->{$type}($line);
    }

    /**
     * Show a comment in terminal.
     *
     * @param $comment
     */
    public function showComment($comment)
    {
        $this->command->comment($comment);
    }

    /**
     * Get the event name.
     *
     * @param $eventCode
     *
     * @return string
     */
    protected function getEventName($eventCode)
    {
        $event = '(unknown event)';

        switch ($eventCode) {
            case Event::RESOURCE_DELETED:
                $event = 'deleted';
                break;
            case Event::RESOURCE_CREATED:
                $event = 'created';
                break;
            case Event::RESOURCE_MODIFIED:
                $event = 'modified';
                break;
        }

        return $event;
    }

    /**
     * Display messages in terminal.
     *
     * @param $messages
     */
    protected function displayMessages($messages)
    {
        $fatal = $messages->reduce(function ($carry, $message) {
            $prefix = $message['type'] == 'error' ? 'FATAL ERROR: ' : '';

            $this->command->{$message['type']}($prefix.$message['body']);

            if ($message['type'] == 'error') {
                return true;
            }

            return $carry;
        });

        if ($fatal == true) {
            die;
        }
    }

    /**
     * Set the command.
     *
     * @param $command
     */
    protected function setCommand($command)
    {
        $this->command = $command;

        if (!is_null($this->loader)) {
            $this->loader->setCommand($this->command);
        }
    }
}
