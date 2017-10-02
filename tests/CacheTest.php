<?php

namespace PragmaRX\TestsWatcher\Tests;

class CacheTest extends TestCase
{
    public function setUp()
    {
        parent::setup();

        $this->cache = app('ci.cache');
    }

    public function test_cache_holds_cached_ip()
    {
        $this->ci->blacklist($ip = '172.17.0.1');

        $this->ci->find($ip);

        $this->assertTrue($this->cache->has($ip.'-----------------'));
    }
}
