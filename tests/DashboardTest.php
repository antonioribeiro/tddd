<?php

namespace PragmaRX\Tddd\Tests;

use PHPUnit\Framework\TestCase;
use PragmaRX\Tddd\Package\Services\Watcher;
use PragmaRX\Tddd\Package\Services\Tester;

class DashboardTest extends TestCase
{
    private $watcher;

    public function setUp()
    {
        parent::setup();

        $this->watcher = app(Watcher::class);

        $this->worker = app(Tester::class);
    }

    public function test_can_instantiate_watcher()
    {
        $this->assertInstanceOf(Watcher::class, $this->watcher);

        $this->assertInstanceOf(Tester::class, $this->worker);
    }
}
