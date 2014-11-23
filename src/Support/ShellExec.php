<?php

namespace PragmaRX\Ci\Support;

use Closure;

use Symfony\Component\Process\Process;

class ShellExec {

	public function exec($command, $runDir = null, Closure $callback = null, $timeout = null)
	{
		// Create a process
		//
		$process = new Process($command, $runDir);

		// Timeout == null === infinite
		//
		$process->setTimeout($timeout);

		// Execute the process
		//
		$process->run($callback);

		return $process;
	}

	public function execOLD($command, $exec_path = '', Closure $callable = null)
	{
		if ($exec_path)
		{
			$command = 'cd '.$exec_path.'; ' . $command;
		}

		$lines = [$command];

		flush();

		$fp = popen($command, "r");

		while( ! feof($fp))
		{
			// send the current file part to the browser
			$lines[] = $line = fread($fp, 1024);

			if ($callable)
			{
				$callable($line);
			}

			// flush the content to the browser
			flush();
		}

		fclose($fp);

		return $lines;
	}

} 
