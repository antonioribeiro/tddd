<?php

namespace PragmaRX\TestsWatcher\Support;

use Carbon\Carbon;
use Closure;
use Symfony\Component\Process\Process;

class ShellExec
{
    public $time;

    public $startedAt;

    public $endedAt;

    public function exec($command, $runDir = null, Closure $callback = null, $timeout = null)
    {
        $process = new Process($command, $runDir);

        $process->setTimeout($timeout);

        $this->startedAt = Carbon::now();

        $process->run($callback);

        $this->endedAt = Carbon::now();

        return $process;
    }

    public function elapsedForHumans()
    {
        return $this->endedAt->diffForHumans($this->startedAt);
    }
}
