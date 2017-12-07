<?php

namespace PragmaRX\TestsWatcher\Package\Console\Commands;

class ClearCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tddd:clear';

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
     */
    public function handle()
    {
        app('tddd')->clear();

        $this->info('Cleared.');
    }
}
