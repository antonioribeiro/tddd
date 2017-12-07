<?php

namespace PragmaRX\Tddd\Package\Console\Commands;

class WatchCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tddd:watch {--show-tests : Show watched tests}';

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
    public function handle()
    {
        $this->getLaravel()->make('tddd.watcher')->run($this, $this->option('show-tests'));
    }
}
