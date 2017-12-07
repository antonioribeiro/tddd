<?php

namespace PragmaRX\Tddd\Package\Console\Commands;

use Illuminate\Console\Command;

class BaseCommand extends Command
{
    /**
     * Draw a line in console.
     *
     * @param int $len
     */
    public function drawLine($len = 80)
    {
        if (is_string($len)) {
            $len = strlen($len);
        }

        $this->line(str_repeat('-', max($len, 80)));
    }
}
