<?php

namespace PragmaRX\TestsWatcher\Package\Console\Commands;

class WatchCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tddd:watch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Watch for file changes';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function fire()
    {
        $this->getLaravel()->make('tddd.watcher')->run($this);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->fire();
    }
}
