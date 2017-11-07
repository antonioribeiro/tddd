<?php

namespace PragmaRX\TestsWatcher\Package\Console\Commands;

class TestCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tddd:work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Continuously run tests';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handle command.
     */
    public function handle()
    {
        $this->getLaravel()->make('tddd.tester')->run($this);
    }
}
