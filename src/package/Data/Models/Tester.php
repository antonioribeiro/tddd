<?php

namespace PragmaRX\TestsWatcher\Package\Data\Models;

class Tester extends Model
{
    protected $table = 'tddd_testers';

    protected $fillable = [
        'name',
        'command',
        'output_folder',
        'output_html_fail_extension',
        'output_png_fail_extension',
        'pipers',
        'error_pattern',
        'env',
    ];

    public function getPipersAttribute($value)
    {
        $pipers = collect(json_decode($value));

        return $pipers->mapWithKeys(function ($piper) {
            return [$piper => __config("pipers.{$piper}")];
        });
    }

    public function setPipersAttribute($value)
    {
        $this->attributes['pipers'] = collect($value)->toJson();
    }
}
