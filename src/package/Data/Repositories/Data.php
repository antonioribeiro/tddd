<?php

namespace PragmaRX\Tddd\Package\Data\Repositories;

use PragmaRX\Tddd\Package\Data\Repositories\Support\Helpers;
use PragmaRX\Tddd\Package\Data\Repositories\Support\Messages;
use PragmaRX\Tddd\Package\Data\Repositories\Support\Notifications;
use PragmaRX\Tddd\Package\Data\Repositories\Support\Projects;
use PragmaRX\Tddd\Package\Data\Repositories\Support\Suites;
use PragmaRX\Tddd\Package\Data\Repositories\Support\Testers;
use PragmaRX\Tddd\Package\Data\Repositories\Support\Tests;
use PragmaRX\Tddd\Package\Support\Notifier;
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
