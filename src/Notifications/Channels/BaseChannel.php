<?php

namespace PragmaRX\TestsWatcher\Notifications\Channels;

abstract class BaseChannel implements Contract
{
    private function getActionMessage($item)
    {
        return isset($item['action_message'])
                ? $item['action_message']
                : (
                        config('ci.notifications.action_message')
                        ?:
                        config('ci.notifications.action-message') /// TODO: deprecate
                    );
    }

    /**
     * @return mixed
     */
    protected function getActionTitle()
    {
        return config('ci.notifications.action-title');
    }

    /**
     * @param $item
     *
     * @return string
     */
    protected function getMessage($item)
    {
        return $this->getActionMessage($item);
    }

    /**
     * @return string
     */
    protected function getActionLink()
    {
        return route(config('ci.notifications.routes.dashboard'));
    }

    protected function makeActionTitle($test)
    {
        return "{$test['project_name']} - {$test['name']}";
    }

    protected function makeActionLink($test)
    {
        return route(
            'tests-watcher.dashboard',
            [
                'test_id'    => $test['id'],
                'project_id' => $test['project_id'],
            ]
        );
    }
}
