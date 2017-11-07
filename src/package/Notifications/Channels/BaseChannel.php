<?php

namespace PragmaRX\TestsWatcher\Package\Notifications\Channels;

abstract class BaseChannel implements Contract
{
    private function getActionMessage($item)
    {
        return isset($item['action_message'])
                ? $item['action_message']
                : (
                        config('tddd.notifications.action_message')
                        ?:
                        config('tddd.notifications.action-message') /// TODO: deprecate
                    );
    }

    /**
     * @return mixed
     */
    protected function getActionTitle()
    {
        return config('tddd.notifications.action-title');
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
        return route(config('tddd.notifications.routes.dashboard'));
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
