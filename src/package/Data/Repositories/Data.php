<?php

namespace PragmaRX\TestsWatcher\Package\Data\Repositories;

use PragmaRX\TestsWatcher\Package\Data\Repositories\Support\Helpers;
use PragmaRX\TestsWatcher\Package\Data\Repositories\Support\Messages;
use PragmaRX\TestsWatcher\Package\Data\Repositories\Support\Notifications;
use PragmaRX\TestsWatcher\Package\Data\Repositories\Support\Projects;
use PragmaRX\TestsWatcher\Package\Data\Repositories\Support\Suites;
use PragmaRX\TestsWatcher\Package\Data\Repositories\Support\Testers;
use PragmaRX\TestsWatcher\Package\Data\Repositories\Support\Tests;
use PragmaRX\TestsWatcher\Package\Support\Notifier;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class Data
{
    use Helpers, Tests, Testers, Projects, Notifications, Messages, Suites;

    /**
     * Data constructor.
     *
     * @param Notifier $notifier
     */
    public function __construct(Notifier $notifier)
    {
        $this->setAnsiConverter(new AnsiToHtmlConverter());

        $this->setNotifier($notifier);

        $this->setMessages(collect());
    }
}
