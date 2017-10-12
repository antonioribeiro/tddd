<?php

namespace PragmaRX\TestsWatcher\Tests;

use PHPUnit\Framework\TestCase;
use PragmaRX\TestsWatcher\Package\Services\Watcher;

class DashboardTest extends TestCase
{
    private $watcher;

    public function setUp()
    {
        parent::setup();

        $this->watcher = app(Watcher::class);
    }

    public function test_can_instantiate_watcher()
    {
        $this->assertInstanceOf(Watcher::class, $this->watcher);
    }
}
