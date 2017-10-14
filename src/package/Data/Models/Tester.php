<?php

namespace PragmaRX\TestsWatcher\Package\Data\Models;

class Tester extends Model
{
    protected $table = 'ci_testers';

    protected $fillable = [
        'name',
        'command',
        'output_folder',
        'output_html_fail_extension',
        'output_png_fail_extension',
        'require_tee',
        'require_script',
        'error_pattern',
        'env',
    ];
}
