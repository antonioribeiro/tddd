<?php

namespace PragmaRX\Ci\Vendor\Laravel\Console\Commands;

class WatchCommand extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ci:watch';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Watch for file changes';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->getLaravel()->make('ci.watcher')->run($this);
	}

}
