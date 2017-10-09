<?php

namespace PragmaRX\TestsWatcher\Vendor\Laravel\Console\Commands;

class ClearCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ci:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all records from the runs table';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function fire()
    {
        app('ci')->clear();

        $this->info('Cleared.');
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->fire();
    }
}
