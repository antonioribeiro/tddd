<?php

namespace PragmaRX\TestsWatcher\Services;

use JasonLewis\ResourceWatcher\Event;

class Base
{
    /**
     * The command object.
     *
     * @object \PragmaRX\TestsWatcher\Vendor\Laravel\Console\Commands\BaseCommand
     */
    protected $command;

    /**
     * Get a configuration key.
     *
     * @param $key
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function getConfig($key)
    {
        if (is_null($value = config("ci.{$key}"))) {
            throw new \Exception("The configuration key '{$key}' was not defined.");
        }

        return $value;
    }

    /**
     * Show progress in terminal.
     *
     * @param $line
     */
    public function showProgress($line, $addSeparator = false)
    {
        if ($addSeparator) {
            $this->command->drawLine($line = 'Executing '.$line);
        }

        $this->command->line($line);
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
}
