<?php

namespace PragmaRX\Tddd\Package\Data\Repositories\Support;

use PragmaRX\Tddd\Package\Support\Notifier;

trait Notifications
{
    /**
     * @var Notifier
     */
    protected $notifier;

    /**
     * @return Notifier
     */
    public function getNotifier()
    {
        return $this->notifier;
    }

    /**
     * Notify users.
     *
     * @param $project_id
     */
    public function notify($project_id)
    {
        $this->notifier->notifyViaChannels(
            $this->getProjectTests($project_id)->reject(function ($item) {
                return $item['state'] != 'failed' && is_null($item['notified_at']);
            })
        );
    }

    /**
     * @param Notifier $notifier
     */
    public function setNotifier($notifier)
    {
        $this->notifier = $notifier;
    }
}
