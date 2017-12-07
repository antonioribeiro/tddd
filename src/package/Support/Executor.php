<?php

namespace PragmaRX\Tddd\Package\Support;

use Carbon\Carbon;
use Closure;
use Symfony\Component\Process\Process;

class Executor
{
    public $time;

    public $startedAt;

    public $endedAt;

    /**
     * Execute one command.
     *
     * @param $command
     * @param null         $runDir
     * @param Closure|null $callback
     * @param null         $timeout
     *
     * @return Process
     */
    public function exec($command, $runDir = null, Closure $callback = null, $timeout = null)
    {
        $process = new Process($command, $runDir);

        $process->setTimeout($timeout);

        $this->startedAt = Carbon::now();

        $process->run($callback);

        $this->endedAt = Carbon::now();

        return $process;
    }

    /**
     * Get the elapsed time formatted for humans.
     *
     * @return mixed
     */
    public function elapsedForHumans()
    {
        return $this->endedAt->diffForHumans($this->startedAt);
    }

    /**
     * Execute a shell command.
     *
     * @param $command
     *
     * @return mixed
     */
    public function shellExec($command)
    {
        return shell_exec($command);
    }
}
