<?php

namespace PragmaRX\TestsWatcher\Support;

use Closure;
use Carbon\Carbon;
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
