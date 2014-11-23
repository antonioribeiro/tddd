<?php

namespace PragmaRX\Ci\Vendor\Laravel\Console\Commands;

use Illuminate\Console\Command;

class BaseCommand extends Command {

	public function drawLine($len = 80)
	{
		if (is_string($len))
		{
			$len = strlen($len);
		}

		$this->line(str_repeat('-', max($len, 80)));
	}

}
